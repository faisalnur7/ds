<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CheckoutRequestController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberDocumentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfitDistributionController;
use App\Http\Controllers\Admin\ProjectIncomeController;
use App\Http\Controllers\Admin\ProjectMemberController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShareSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->get('/', [AuthenticatedSessionController::class, 'create'])->name('home');

Route::get('/dashboard', function () {
    return auth()->user()?->isAdmin()
        ? redirect()->route('admin.dashboard')
        : view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('users', UserController::class)->middleware('permission:manage_roles');
    Route::resource('roles', RoleController::class)->middleware('permission:manage_roles');
    Route::resource('permissions', PermissionController::class)->middleware('permission:manage_permissions');
    Route::resource('settings', SettingController::class)->middleware('permission:manage_settings');
    Route::resource('share-settings', ShareSettingController::class)->middleware('permission:manage_settings');
    Route::resource('members', MemberController::class)->middleware('permission:manage_members');
    Route::resource('member-documents', MemberDocumentController::class)->middleware('permission:manage_members');
    Route::resource('payments', PaymentController::class)->middleware('permission:manage_payments');
    Route::resource('projects', ProjectController::class)->middleware('permission:manage_projects');
    Route::resource('project-members', ProjectMemberController::class)->middleware('permission:manage_projects');
    Route::resource('project-incomes', ProjectIncomeController::class)->middleware('permission:manage_projects');
    Route::resource('profit-distributions', ProfitDistributionController::class)->middleware('permission:manage_profits');
    Route::resource('loans', LoanController::class)->middleware('permission:manage_loans');
    Route::resource('checkout-requests', CheckoutRequestController::class)->middleware('permission:manage_checkout');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->middleware('permission:view_audit_logs')->name('audit.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
