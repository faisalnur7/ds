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
                'view_users',
                'create_users',
                'edit_users',
                'update_users',
                'delete_users',
                'view_roles',
                'create_roles',
                'edit_roles',
                'update_roles',
                'delete_roles',
                'view_permissions',
                'create_permissions',
                'edit_permissions',
                'update_permissions',
                'delete_permissions',
                'view_settings',
                'create_settings',
                'edit_settings',
                'update_settings',
                'delete_settings',
                'view_share_settings',
                'create_share_settings',
                'edit_share_settings',
                'update_share_settings',
                'delete_share_settings',
                'view_members',
                'create_members',
                'edit_members',
                'update_members',
                'delete_members',
                'view_member_documents',
                'create_member_documents',
                'edit_member_documents',
                'update_member_documents',
                'delete_member_documents',
                'view_payments',
                'create_payments',
                'edit_payments',
                'update_payments',
                'delete_payments',
                'view_projects',
                'create_projects',
                'edit_projects',
                'update_projects',
                'delete_projects',
                'view_project_members',
                'create_project_members',
                'edit_project_members',
                'update_project_members',
                'delete_project_members',
                'view_project_incomes',
                'create_project_incomes',
                'edit_project_incomes',
                'update_project_incomes',
                'delete_project_incomes',
                'view_profit_distributions',
                'create_profit_distributions',
                'edit_profit_distributions',
                'update_profit_distributions',
                'delete_profit_distributions',
                'view_loans',
                'create_loans',
                'edit_loans',
                'update_loans',
                'delete_loans',
                'view_checkout_requests',
                'create_checkout_requests',
                'edit_checkout_requests',
                'update_checkout_requests',
                'delete_checkout_requests',
                'view_expense_menu',
                'view_expense_categories',
                'create_expense_categories',
                'edit_expense_categories',
                'update_expense_categories',
                'delete_expense_categories',
                'view_expenses',
                'create_expenses',
                'edit_expenses',
                'update_expenses',
                'delete_expenses',
                'approve_expenses',
                'view_audit_logs',
                'view_payment_history',
            ],
            'admin' => [
                'view_dashboard',
                'view_users',
                'create_users',
                'edit_users',
                'update_users',
                'delete_users',
                'view_roles',
                'create_roles',
                'edit_roles',
                'update_roles',
                'delete_roles',
                'view_permissions',
                'create_permissions',
                'edit_permissions',
                'update_permissions',
                'delete_permissions',
                'view_settings',
                'create_settings',
                'edit_settings',
                'update_settings',
                'delete_settings',
                'view_share_settings',
                'create_share_settings',
                'edit_share_settings',
                'update_share_settings',
                'delete_share_settings',
                'view_members',
                'create_members',
                'edit_members',
                'update_members',
                'delete_members',
                'view_member_documents',
                'create_member_documents',
                'edit_member_documents',
                'update_member_documents',
                'delete_member_documents',
                'view_payments',
                'create_payments',
                'edit_payments',
                'update_payments',
                'delete_payments',
                'view_projects',
                'create_projects',
                'edit_projects',
                'update_projects',
                'delete_projects',
                'view_project_members',
                'create_project_members',
                'edit_project_members',
                'update_project_members',
                'delete_project_members',
                'view_project_incomes',
                'create_project_incomes',
                'edit_project_incomes',
                'update_project_incomes',
                'delete_project_incomes',
                'view_profit_distributions',
                'create_profit_distributions',
                'edit_profit_distributions',
                'update_profit_distributions',
                'delete_profit_distributions',
                'view_loans',
                'create_loans',
                'edit_loans',
                'update_loans',
                'delete_loans',
                'view_checkout_requests',
                'create_checkout_requests',
                'edit_checkout_requests',
                'update_checkout_requests',
                'delete_checkout_requests',
                'view_expense_menu',
                'view_expense_categories',
                'create_expense_categories',
                'edit_expense_categories',
                'update_expense_categories',
                'delete_expense_categories',
                'view_expenses',
                'create_expenses',
                'edit_expenses',
                'update_expenses',
                'delete_expenses',
                'approve_expenses',
                'view_audit_logs',
                'view_payment_history',
            ],
            'cashier' => [
                'view_dashboard',
                'view_payments',
                'create_payments',
                'edit_payments',
                'update_payments',
                'view_expense_categories',
                'create_expense_categories',
                'edit_expense_categories',
                'update_expense_categories',
                'delete_expense_categories',
                'view_expenses',
                'create_expenses',
                'edit_expenses',
                'update_expenses',
                'delete_expenses',
                'approve_expenses',
            ],
            'auditor' => ['view_dashboard', 'view_audit_logs'],
            'member' => ['view_dashboard', 'view_payment_history'],
            default => ['view_dashboard'],
        };
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (in_array($permission, $this->permissions(), true)) {
            return true;
        }

        $legacyPermission = $this->legacyPermissionFor($permission);

        return $legacyPermission !== null && in_array($legacyPermission, $this->permissions(), true);
    }

    protected function legacyPermissionFor(string $permission): ?string
    {
        $parts = explode('_', $permission, 2);

        if (count($parts) !== 2) {
            return null;
        }

        [$action, $subject] = $parts;

        if (! in_array($action, ['view', 'create', 'edit', 'update', 'delete'], true)) {
            return null;
        }

        return match ($subject) {
            'users', 'roles' => 'manage_roles',
            'permissions' => 'manage_permissions',
            'settings', 'share_settings' => 'manage_settings',
            'members', 'member_documents' => 'manage_members',
            'payments' => 'manage_payments',
                'projects', 'project_members', 'project_incomes' => 'manage_projects',
                'profit_distributions' => 'manage_profits',
                'loans' => 'manage_loans',
                'checkout_requests' => 'manage_checkout',
                'expenses', 'expense_categories' => 'manage_expenses',
                default => "manage_{$subject}",
            };
        }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
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
