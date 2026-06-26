<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CheckoutRequest;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProfitDistribution;
use App\Models\Setting;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function members(): View
    {
        return $this->renderModule(
            'Member Registry',
            'Member onboarding, KYC, and lifecycle data.',
            Member::query()->latest()->with('user')->paginate(10),
        );
    }

    public function payments(): View
    {
        return $this->renderModule(
            'Payments',
            'Approved, pending, late, and partial share payments.',
            Payment::query()->latest()->with('member')->paginate(10),
        );
    }

    public function projects(): View
    {
        return $this->renderModule(
            'Projects',
            'Investment projects and recurring income entries.',
            Project::query()->latest()->paginate(10),
        );
    }

    public function profits(): View
    {
        return $this->renderModule(
            'Profit Distribution',
            'Historical profit payout records and references.',
            ProfitDistribution::query()->latest()->with(['project', 'member'])->paginate(10),
        );
    }

    public function checkout(): View
    {
        return $this->renderModule(
            'Checkout',
            'Full and partial checkout requests with loan deductions.',
            CheckoutRequest::query()->latest()->with('member')->paginate(10),
        );
    }

    public function loans(): View
    {
        return $this->renderModule(
            'Loans',
            'Loan against share requests and repayment planning.',
            Loan::query()->latest()->with('member')->paginate(10),
        );
    }

    public function settings(): View
    {
        return $this->renderModule(
            'Settings',
            'Typed key-value settings and share configuration.',
            Setting::query()->latest()->paginate(10),
        );
    }

    public function audit(): View
    {
        return $this->renderModule(
            'Audit Logs',
            'Read-only activity log for compliance and review.',
            AuditLog::query()->latest('created_at')->paginate(10),
        );
    }

    private function renderModule(string $title, string $description, mixed $items): View
    {
        return view('admin.modules.index', [
            'title' => $title,
            'description' => $description,
            'items' => $items,
        ]);
    }
}
