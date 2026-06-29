<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $member = $user?->member;

        abort_unless($member instanceof Member, 403, 'You do not have access to the member dashboard.');

        $approvedPayments = $member->payments()->where('status', 'approved');
        $totalContributed = (float) $approvedPayments->sum('amount_paid');
        $paymentCount = (int) $approvedPayments->count();
        $latestPayment = $member->payments()->latest('payment_month')->latest('id')->first();
        $latestCheckoutRequest = $member->checkoutRequests()->latest('requested_at')->latest('id')->first();
        $eligibleOn = $member->checkout_eligible_on;

        return view('dashboard', [
            'member' => $member,
            'summary' => [
                'shareNumber' => (int) $member->share_number,
                'approvedPayments' => $paymentCount,
                'totalContributed' => $totalContributed,
                'documents' => $member->documents()->count(),
                'checkoutRequests' => $member->checkoutRequests()->count(),
                'checkoutEligibleOn' => $eligibleOn?->toDateString(),
            ],
            'latestPayment' => $latestPayment,
            'latestCheckoutRequest' => $latestCheckoutRequest,
            'recentPayments' => $member->payments()->latest('payment_month')->latest('id')->limit(6)->get(),
            'recentCheckoutRequests' => $member->checkoutRequests()->latest('requested_at')->latest('id')->limit(6)->get(),
            'recentDocuments' => $member->documents()->latest('uploaded_at')->latest('id')->limit(4)->get(),
            'shareHistory' => $member->shareHistories()->latest('changed_at')->limit(5)->get(),
        ]);
    }
}
