<?php

namespace App\Http\Controllers;

use App\Models\CheckoutRequest;
use App\Models\Member;
use App\Models\ShareSetting;
use App\Notifications\MemberCheckoutRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberCheckoutRequestController extends Controller
{
    public function index(Request $request): View
    {
        $member = $request->user()?->member;

        abort_unless($member instanceof Member, 403, 'You do not have access to checkout requests.');

        $member->loadMissing(['user']);
        $approvedPayments = $member->payments()->where('status', 'approved');
        $availableAmount = (float) $approvedPayments->sum('amount_paid');
        $latestRequest = $member->checkoutRequests()->latest('requested_at')->latest('id')->first();
        $requests = $member->checkoutRequests()
            ->with('approver')
            ->latest('requested_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('member.checkout-requests.index', [
            'member' => $member,
            'currentShareSetting' => ShareSetting::current(),
            'availableAmount' => $availableAmount,
            'latestRequest' => $latestRequest,
            'requests' => $requests,
            'checkoutEligibleOn' => $member->checkout_eligible_on,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $member = $request->user()?->member;

        abort_unless($member instanceof Member, 403, 'You do not have access to checkout requests.');

        if ($eligibleOn = $member->checkout_eligible_on) {
            abort_if(now()->lt($eligibleOn), 422, 'This member is not yet eligible for checkout.');
        }

        $data = $request->validate([
            'checkout_type' => ['required', 'in:full,partial'],
            'partial_percentage' => ['nullable', 'required_if:checkout_type,partial', 'numeric', 'min:1', 'max:100'],
            'outstanding_loan_deducted' => ['nullable', 'numeric', 'min:0'],
        ]);

        $approvedPayments = (float) $member->payments()->where('status', 'approved')->sum('amount_paid');
        $outstandingLoanDeducted = (float) ($data['outstanding_loan_deducted'] ?? 0);
        $partialPercentage = $data['checkout_type'] === 'partial' ? (float) $data['partial_percentage'] : null;
        $refundableAmount = $this->calculateRefundableAmount(
            approvedPayments: $approvedPayments,
            checkoutType: $data['checkout_type'],
            partialPercentage: $partialPercentage,
            outstandingLoanDeducted: $outstandingLoanDeducted,
        );

        $requestRecord = CheckoutRequest::query()->create([
            'member_id' => $member->id,
            'requested_at' => now(),
            'checkout_type' => $data['checkout_type'],
            'partial_percentage' => $partialPercentage,
            'refundable_amount' => $refundableAmount,
            'outstanding_loan_deducted' => $outstandingLoanDeducted,
            'status' => 'pending',
            'approved_by' => null,
            'paid_at' => null,
        ]);

        $member->user?->notify(MemberCheckoutRequestNotification::submitted($requestRecord));

        return redirect()
            ->route('checkout-requests.index')
            ->with('status', 'created');
    }

    private function calculateRefundableAmount(float $approvedPayments, string $checkoutType, ?float $partialPercentage, float $outstandingLoanDeducted): float
    {
        $baseAmount = $checkoutType === 'partial' && $partialPercentage !== null
            ? ($approvedPayments * ($partialPercentage / 100))
            : $approvedPayments;

        return max(round($baseAmount - $outstandingLoanDeducted, 2), 0);
    }
}
