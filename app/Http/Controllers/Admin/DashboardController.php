<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckoutRequest;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProfitDistribution;
use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function __invoke(): View
    {
        $stats = [
            'totalUsers' => User::count(),
            'adminUsers' => User::whereIn('role', ['super_admin', 'admin'])->count(),
            'newToday' => User::whereDate('created_at', today())->count(),
            'verifiedUsers' => User::whereNotNull('email_verified_at')->count(),
            'members' => Member::count(),
            'payments' => Payment::count(),
            'expenseCategories' => ExpenseCategory::count(),
            'expenses' => Expense::count(),
            'projects' => Project::count(),
            'profits' => ProfitDistribution::count(),
            'checkouts' => CheckoutRequest::count(),
            'loans' => Loan::count(),
            'settings' => Setting::count(),
        ];

        $recentUsers = User::query()
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
