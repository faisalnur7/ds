<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CheckoutRequestController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberDocumentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ProfitDistributionController;
use App\Http\Controllers\Admin\ProjectIncomeController;
use App\Http\Controllers\Admin\ProjectMemberController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShareSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MemberCheckoutRequestController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->get('/', [AuthenticatedSessionController::class, 'create'])->name('home');

Route::post('/locale/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['bn', 'en'], true), 404);

    $request->session()->put('locale', $locale);

    return back();
})->name('locale.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/checkout-requests', [MemberCheckoutRequestController::class, 'index'])->name('checkout-requests.index');
    Route::post('/checkout-requests', [MemberCheckoutRequestController::class, 'store'])->name('checkout-requests.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('settings', SettingController::class);
    Route::resource('share-settings', ShareSettingController::class);
    Route::patch('share-settings/{share_setting}/activate', [ShareSettingController::class, 'activate'])
        ->middleware('permission:update_share_settings')
        ->name('share-settings.activate');
    Route::patch('share-settings/{share_setting}/toggle', [ShareSettingController::class, 'toggle'])
        ->middleware('permission:update_share_settings')
        ->name('share-settings.toggle');
    Route::resource('members', MemberController::class);
    Route::resource('member-documents', MemberDocumentController::class);
    Route::resource('payments', PaymentController::class);
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::get('payments/{payment}/receipt/download', [PaymentController::class, 'downloadReceipt'])->name('payments.receipt.download');
    Route::resource('projects', ProjectController::class);
    Route::resource('project-members', ProjectMemberController::class);
    Route::resource('project-incomes', ProjectIncomeController::class);
    Route::resource('profit-distributions', ProfitDistributionController::class);
    Route::resource('checkout-requests', CheckoutRequestController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->middleware('permission:view_audit_logs')->name('audit.index');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('expense-categories', ExpenseCategoryController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::patch('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
});

Route::middleware('auth')->group(function () {
    Route::get('/payment-history', [PaymentHistoryController::class, 'index'])
        ->middleware('permission:view_payment_history')
        ->name('payment-history.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
