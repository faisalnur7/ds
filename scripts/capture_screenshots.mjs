import { spawn } from 'node:child_process';
import { mkdir, writeFile, rm } from 'node:fs/promises';
import path from 'node:path';

const APP_URL = 'http://localhost:8080';
const CHROME = process.env.CHROME_BIN || '/usr/bin/google-chrome';
const DEBUG_PORT = 9222;
const OUT_DIR = path.resolve('public/screenshots');
const PROFILE_DIR = '/tmp/ds-chrome-profile';

const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function fetchJson(url) {
  const res = await fetch(url);
  if (!res.ok) {
    throw new Error(`GET ${url} failed: ${res.status} ${res.statusText}`);
  }
  return res.json();
}

class CdpClient {
  constructor(wsUrl) {
    this.ws = new WebSocket(wsUrl);
    this.nextId = 1;
    this.pending = new Map();
    this.eventHandlers = new Map();

    this.ready = new Promise((resolve, reject) => {
      this.ws.addEventListener('open', resolve);
      this.ws.addEventListener('error', reject);
    });

    this.ws.addEventListener('message', (event) => {
      const message = JSON.parse(event.data);
      if (message.id) {
        const entry = this.pending.get(message.id);
        if (!entry) {
          return;
        }
        this.pending.delete(message.id);
        if (message.error) {
          entry.reject(new Error(message.error.message || 'CDP error'));
        } else {
          entry.resolve(message.result);
        }
        return;
      }

      const handlers = this.eventHandlers.get(message.method) || [];
      for (const handler of handlers) {
        handler(message.params);
      }
    });
  }

  on(method, handler) {
    const handlers = this.eventHandlers.get(method) || [];
    handlers.push(handler);
    this.eventHandlers.set(method, handlers);
  }

  async send(method, params = {}) {
    await this.ready;
    const id = this.nextId++;
    const payload = JSON.stringify({ id, method, params });
    const promise = new Promise((resolve, reject) => {
      this.pending.set(id, { resolve, reject });
    });
    this.ws.send(payload);
    return promise;
  }

  close() {
    this.ws.close();
  }
}

async function startChrome() {
  await rm(PROFILE_DIR, { recursive: true, force: true });

  const chrome = spawn(CHROME, [
    '--headless=new',
    '--no-sandbox',
    '--disable-setuid-sandbox',
    '--disable-gpu',
    '--disable-dev-shm-usage',
    '--disable-background-networking',
    '--disable-default-apps',
    '--disable-extensions',
    '--disable-sync',
    '--metrics-recording-only',
    '--no-first-run',
    '--no-default-browser-check',
    '--hide-scrollbars',
    '--force-device-scale-factor=1',
    `--remote-debugging-port=${DEBUG_PORT}`,
    `--user-data-dir=${PROFILE_DIR}`,
    'about:blank',
  ], { stdio: 'ignore' });

  const versionUrl = `http://127.0.0.1:${DEBUG_PORT}/json/version`;
  for (let i = 0; i < 60; i += 1) {
    try {
      const version = await fetchJson(versionUrl);
      return { chrome, version };
    } catch {
      await delay(250);
    }
  }

  chrome.kill('SIGKILL');
  throw new Error('Chrome did not start');
}

async function createPage(version) {
  const targets = await fetchJson(`http://127.0.0.1:${DEBUG_PORT}/json/list`);
  const target = targets.find((entry) => entry.type === 'page') || targets[0];
  const client = new CdpClient(target.webSocketDebuggerUrl || version.webSocketDebuggerUrl);
  await client.ready;
  await client.send('Page.enable');
  await client.send('Runtime.enable');
  await client.send('Network.enable');
  await client.send('Emulation.setDeviceMetricsOverride', {
    width: 1440,
    height: 2000,
    deviceScaleFactor: 1,
    mobile: false,
  });
  await client.send('Network.setBlockedURLs', {
    urls: ['*fonts.bunny.net*'],
  });
  return client;
}

async function waitForReady(client, timeoutMs = 15000) {
  const start = Date.now();
  while (Date.now() - start < timeoutMs) {
    const result = await client.send('Runtime.evaluate', {
      expression: 'document.readyState',
      returnByValue: true,
    });
    if (result.result?.value === 'complete') {
      return;
    }
    await delay(150);
  }
  throw new Error('Timed out waiting for page readiness');
}

async function navigate(client, url) {
  await client.send('Page.navigate', { url });
  await waitForReady(client);
  await delay(350);
}

async function currentPath(client) {
  const result = await client.send('Runtime.evaluate', {
    expression: 'location.pathname + location.search',
    returnByValue: true,
  });
  return result.result?.value || '';
}

async function fillAndSubmit(client, fields) {
  const script = `
    (() => {
      const setValue = (selector, value) => {
        const el = document.querySelector(selector);
        if (!el) throw new Error('Missing field: ' + selector);
        const proto = Object.getPrototypeOf(el);
        const desc = Object.getOwnPropertyDescriptor(proto, 'value');
        desc.set.call(el, value);
        el.dispatchEvent(new Event('input', { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
      };
      ${Object.entries(fields)
        .map(([selector, value]) => `setValue(${JSON.stringify(selector)}, ${JSON.stringify(value)});`)
        .join('\n')}
      const form = document.querySelector('form');
      if (!form) throw new Error('Missing form');
      form.submit();
    })();
  `;
  await client.send('Runtime.evaluate', { expression: script });
  await waitForReady(client);
  await delay(400);
}

async function clickTextButton(client, text) {
  const script = `
    (() => {
      const buttons = [...document.querySelectorAll('button, a')];
      const target = buttons.find((el) => (el.textContent || '').trim() === ${JSON.stringify(text)});
      if (!target) throw new Error('Missing button: ${text}');
      target.click();
    })();
  `;
  await client.send('Runtime.evaluate', { expression: script });
  await waitForReady(client);
  await delay(250);
}

async function logout(client) {
  await client.send('Runtime.evaluate', {
    expression: `
      (() => {
        const form = document.querySelector('form[action$="/logout"]');
        if (form) {
          form.submit();
          return true;
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) throw new Error('Missing CSRF token');
        return fetch('/logout', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
          },
          body: '{}',
        }).then(() => true);
      })();
    `,
    awaitPromise: true,
  });
  await navigate(client, `${APP_URL}/login`);
}

async function login(client, email, password) {
  await navigate(client, `${APP_URL}/login`);
  await fillAndSubmit(client, {
    'input[name="email"]': email,
    'input[name="password"]': password,
  });
}

async function capture(client, url, file) {
  await navigate(client, url);
  await client.send('Emulation.setDeviceMetricsOverride', {
    width: 1440,
    height: 2000,
    deviceScaleFactor: 1,
    mobile: false,
  });
  const screenshot = await client.send('Page.captureScreenshot', {
    format: 'png',
    captureBeyondViewport: true,
    fromSurface: true,
  });
  const fullPath = path.join(OUT_DIR, file);
  await mkdir(path.dirname(fullPath), { recursive: true });
  await writeFile(fullPath, Buffer.from(screenshot.data, 'base64'));
  console.log(`saved ${file} <- ${url}`);
}

async function main() {
  await mkdir(OUT_DIR, { recursive: true });

  const { chrome, version } = await startChrome();
  const client = await createPage(version);

  try {
    const guestPages = [
      ['/login', 'guest/login.png'],
      ['/register', 'guest/register.png'],
      ['/forgot-password', 'guest/forgot-password.png'],
      ['/reset-password/demo-token', 'guest/reset-password.png'],
    ];

    for (const [url, file] of guestPages) {
      await capture(client, `${APP_URL}${url}`, file);
    }

    await client.send('Runtime.evaluate', {
      expression: `
        document.documentElement.innerHTML = '';
        document.body.innerHTML = '';
      `,
    });

    await runMemberPass(client);
    await runAdminPass(client);
  } finally {
    client.close();
    chrome.kill('SIGKILL');
  }
}

async function runMemberPass(client) {
  const email = 'member@example.com';
  const password = 'password';

  await login(client, email, password);

  const memberPages = [
    ['/dashboard', 'member/dashboard.png'],
    ['/profile', 'member/profile.png'],
    ['/payment-history', 'member/payment-history.png'],
    ['/confirm-password', 'member/confirm-password.png'],
  ];

  for (const [url, file] of memberPages) {
    await capture(client, `${APP_URL}${url}`, file);
  }

  await writeFile(
    path.join('/tmp', 'ds-member-unverified.txt'),
    'toggle before verify-email'
  );
  await toggleVerification('member@example.com', 'unverified');
  await login(client, email, password);
  await capture(client, `${APP_URL}/verify-email`, 'member/verify-email.png');
  await toggleVerification('member@example.com', 'verified');

  await logout(client);
}

async function runAdminPass(client) {
  const email = 'admin@example.com';
  const password = 'password';

  await login(client, email, password);

  const indexPages = [
    ['admin', 'admin/dashboard/index.png'],
    ['admin/users', 'admin/users/index.png'],
    ['admin/roles', 'admin/roles/index.png'],
    ['admin/permissions', 'admin/permissions/index.png'],
    ['admin/settings', 'admin/settings/index.png'],
    ['admin/share-settings', 'admin/share-settings/index.png'],
    ['admin/members', 'admin/members/index.png'],
    ['admin/member-documents', 'admin/member-documents/index.png'],
    ['admin/payments', 'admin/payments/index.png'],
    ['admin/projects', 'admin/projects/index.png'],
    ['admin/project-members', 'admin/project-members/index.png'],
    ['admin/project-incomes', 'admin/project-incomes/index.png'],
    ['admin/profit-distributions', 'admin/profit-distributions/index.png'],
    ['admin/checkout-requests', 'admin/checkout-requests/index.png'],
    ['admin/audit-logs', 'admin/audit-logs/index.png'],
  ];

  for (const [base, file] of indexPages) {
    await capture(client, `${APP_URL}/${base}`, file);
  }

  const resourceScreens = [
    ['admin/users', 'admin/users/create.png', 'create'],
    ['admin/users/1', 'admin/users/show.png', 'show'],
    ['admin/users/1/edit', 'admin/users/edit.png', 'edit'],
    ['admin/roles/create', 'admin/roles/create.png', 'create'],
    ['admin/roles/1', 'admin/roles/show.png', 'show'],
    ['admin/roles/1/edit', 'admin/roles/edit.png', 'edit'],
    ['admin/permissions/create', 'admin/permissions/create.png', 'create'],
    ['admin/permissions/1', 'admin/permissions/show.png', 'show'],
    ['admin/permissions/1/edit', 'admin/permissions/edit.png', 'edit'],
    ['admin/settings/create', 'admin/settings/create.png', 'create'],
    ['admin/settings/1', 'admin/settings/show.png', 'show'],
    ['admin/settings/1/edit', 'admin/settings/edit.png', 'edit'],
    ['admin/share-settings/create', 'admin/share-settings/create.png', 'create'],
    ['admin/share-settings/1', 'admin/share-settings/show.png', 'show'],
    ['admin/share-settings/1/edit', 'admin/share-settings/edit.png', 'edit'],
    ['admin/members/create', 'admin/members/create.png', 'create'],
    ['admin/members/1', 'admin/members/show.png', 'show'],
    ['admin/members/1/edit', 'admin/members/edit.png', 'edit'],
    ['admin/member-documents/create', 'admin/member-documents/create.png', 'create'],
    ['admin/member-documents/1', 'admin/member-documents/show.png', 'show'],
    ['admin/member-documents/1/edit', 'admin/member-documents/edit.png', 'edit'],
    ['admin/payments/create', 'admin/payments/create.png', 'create'],
    ['admin/payments/1', 'admin/payments/show.png', 'show'],
    ['admin/payments/1/edit', 'admin/payments/edit.png', 'edit'],
    ['admin/projects/create', 'admin/projects/create.png', 'create'],
    ['admin/projects/1', 'admin/projects/show.png', 'show'],
    ['admin/projects/1/edit', 'admin/projects/edit.png', 'edit'],
    ['admin/project-members/create', 'admin/project-members/create.png', 'create'],
    ['admin/project-members/1', 'admin/project-members/show.png', 'show'],
    ['admin/project-members/1/edit', 'admin/project-members/edit.png', 'edit'],
    ['admin/project-incomes/create', 'admin/project-incomes/create.png', 'create'],
    ['admin/project-incomes/1', 'admin/project-incomes/show.png', 'show'],
    ['admin/project-incomes/1/edit', 'admin/project-incomes/edit.png', 'edit'],
    ['admin/profit-distributions/create', 'admin/profit-distributions/create.png', 'create'],
    ['admin/profit-distributions/1', 'admin/profit-distributions/show.png', 'show'],
    ['admin/profit-distributions/1/edit', 'admin/profit-distributions/edit.png', 'edit'],
    ['admin/checkout-requests/create', 'admin/checkout-requests/create.png', 'create'],
    ['admin/checkout-requests/1', 'admin/checkout-requests/show.png', 'show'],
    ['admin/checkout-requests/1/edit', 'admin/checkout-requests/edit.png', 'edit'],
  ];

  for (const [url, file] of resourceScreens) {
    await capture(client, `${APP_URL}/${url}`, file);
  }

  await logout(client);
}

async function toggleVerification(email, state) {
  const { stdout, stderr, status } = await new Promise((resolve) => {
    const child = spawn('docker', ['compose', 'exec', '-T', 'laravel.test', 'php', 'scripts/set_user_verified.php', email, state], {
      cwd: process.cwd(),
      stdio: ['ignore', 'pipe', 'pipe'],
    });
    let stdout = '';
    let stderr = '';
    child.stdout.on('data', (chunk) => {
      stdout += chunk;
    });
    child.stderr.on('data', (chunk) => {
      stderr += chunk;
    });
    child.on('close', (code) => resolve({ stdout, stderr, status: code }));
  });

  if (status !== 0) {
    throw new Error(`Failed to set ${email} ${state}: ${stderr || stdout}`);
  }
}

main().catch(async (error) => {
  console.error(error);
  process.exitCode = 1;
});
