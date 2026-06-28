<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = [
    'users',
    'members',
    'payments',
    'projects',
    'project_members',
    'project_incomes',
    'profit_distributions',
    'loans',
    'checkout_requests',
    'share_settings',
    'settings',
    'permissions',
    'roles',
    'audit_logs',
    'member_documents',
];

foreach ($tables as $table) {
    $id = Illuminate\Support\Facades\DB::table($table)->orderBy('id')->value('id');
    echo $table . ':' . ($id ?? 'null') . PHP_EOL;
}

echo PHP_EOL . "users:" . PHP_EOL;
foreach (App\Models\User::query()->orderBy('id')->get(['id', 'email', 'role', 'is_admin', 'email_verified_at']) as $user) {
    echo $user->id . '|' . $user->email . '|' . $user->role . '|' . (int) $user->is_admin . '|' . ($user->email_verified_at ? 'verified' : 'unverified') . PHP_EOL;
}
