<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'is_admin',
        'two_factor_secret',
        'two_factor_enabled',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'slug')->withDefault([
            'name' => ucfirst((string) $this->role ?: 'Member'),
            'slug' => $this->role ?: 'member',
        ]);
    }

    public function permissions(): array
    {
        $permissions = $this->roleModel->permissions()->pluck('slug')->all();

        if (! empty($permissions)) {
            return $permissions;
        }

        return match ($this->role) {
            'super_admin' => [
                'view_dashboard',
                'manage_members',
                'manage_payments',
                'manage_projects',
                'manage_profits',
                'manage_loans',
                'manage_checkout',
                'manage_settings',
                'view_audit_logs',
                'manage_roles',
                'manage_permissions',
            ],
            'admin' => [
                'view_dashboard',
                'manage_members',
                'manage_payments',
                'manage_projects',
                'manage_profits',
                'manage_loans',
                'manage_checkout',
                'manage_settings',
                'view_audit_logs',
                'manage_roles',
                'manage_permissions',
            ],
            'cashier' => ['view_dashboard', 'manage_payments'],
            'auditor' => ['view_dashboard', 'view_audit_logs'],
            default => ['view_dashboard'],
        };
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions(), true);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || (bool) $this->is_admin;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin'], true) || (bool) $this->is_admin;
    }

    public function isAuditor(): bool
    {
        return $this->role === 'auditor';
    }

    public function isMember(): bool
    {
        return $this->role === 'member' || (! $this->isAdmin() && ! $this->isAuditor());
    }
}
