<?php

require __DIR__ . '/../vendor/autoload.php';

$email = $argv[1] ?? null;
$state = $argv[2] ?? null;

if (! $email || ! in_array($state, ['verified', 'unverified'], true)) {
    fwrite(STDERR, "Usage: php scripts/set_user_verified.php <email> <verified|unverified>\n");
    exit(1);
}

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::query()->where('email', $email)->firstOrFail();
$user->email_verified_at = $state === 'verified' ? now() : null;
$user->save();

echo $user->email . ':' . $state . PHP_EOL;

