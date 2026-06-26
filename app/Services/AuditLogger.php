<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogger
{
    public function log(string $action, Model $model, array $oldValues = [], array $newValues = [], ?Request $request = null): AuditLog
    {
        return AuditLog::query()->create([
            'user_id' => $request?->user()?->id,
            'action' => $action,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'created_at' => now(),
        ]);
    }
}
