<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'adminUsers' => User::where('is_admin', true)->count(),
            'newToday' => User::whereDate('created_at', today())->count(),
            'verifiedUsers' => User::whereNotNull('email_verified_at')->count(),
        ];

        $recentUsers = User::query()
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
