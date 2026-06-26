<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ModuleController;
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
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/members', [ModuleController::class, 'members'])->name('members.index');
    Route::get('/payments', [ModuleController::class, 'payments'])->name('payments.index');
    Route::get('/projects', [ModuleController::class, 'projects'])->name('projects.index');
    Route::get('/profits', [ModuleController::class, 'profits'])->name('profits.index');
    Route::get('/checkout', [ModuleController::class, 'checkout'])->name('checkout.index');
    Route::get('/loans', [ModuleController::class, 'loans'])->name('loans.index');
    Route::get('/settings', [ModuleController::class, 'settings'])->name('settings.index');
    Route::get('/audit-logs', [ModuleController::class, 'audit'])->name('audit.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
