<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $member = $request->user()?->member;

        abort_unless($member, 403, 'You do not have access to payment history.');

        $payments = $member->payments()
            ->latest('payment_month')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('payments.history', [
            'member' => $member,
            'payments' => $payments,
        ]);
    }
}
