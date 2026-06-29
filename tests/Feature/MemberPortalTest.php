<?php

namespace Tests\Feature;

use App\Models\CheckoutRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Member;
use App\Models\MemberDocument;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ShareSetting;
use App\Models\User;
use App\Notifications\MemberCheckoutRequestNotification;
use App\Notifications\MemberPaymentNotification;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MemberPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_dashboard_displays_live_metrics(): void
    {
        $member = Member::factory()->create([
            'member_code' => 'DS-9001',
            'full_name' => 'Portal Member',
            'share_number' => 4,
            'join_date' => '2026-01-15',
        ]);

        ShareSetting::query()->create([
            'share_value' => 1000,
            'share_cost' => 50,
            'effective_from' => '2026-01-01',
            'is_active' => true,
        ]);

        Payment::query()->create([
            'member_id' => $member->id,
            'payment_month' => '2026-06-01',
            'due_date' => '2026-06-10',
            'share_value' => 4000,
            'share_cost' => 200,
            'total_amount' => 4200,
            'amount_paid' => 4200,
            'payment_status_detail' => 'full',
            'payment_method' => 'cash',
            'transaction_no' => 'PAY-DASH-001',
            'status' => 'approved',
            'receipt_no' => 'RCPT-DASH-001',
        ]);

        CheckoutRequest::query()->create([
            'member_id' => $member->id,
            'requested_at' => now(),
            'checkout_type' => 'full',
            'partial_percentage' => null,
            'refundable_amount' => 4200,
            'outstanding_loan_deducted' => 0,
            'status' => 'pending',
        ]);

        MemberDocument::query()->create([
            'member_id' => $member->id,
            'doc_type' => 'nid_front',
            'file_path' => 'docs/nid-front.pdf',
            'uploaded_at' => now(),
        ]);

        $this->actingAs($member->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Portal Member')
            ->assertSee('DS-9001')
            ->assertSee('4')
            ->assertSee('4,200.00')
            ->assertSee('Checkout requests')
            ->assertSee('docs/nid-front.pdf');
    }

    public function test_member_can_submit_checkout_request_and_is_notified(): void
    {
        Notification::fake();

        $settings = app(SettingsService::class);
        $settings->put('checkout_eligible_months', 1, 'int');

        $user = User::factory()->create([
            'role' => 'member',
        ]);

        $member = Member::factory()->create([
            'user_id' => $user->id,
            'join_date' => now()->subMonths(2)->toDateString(),
        ]);

        Payment::query()->create([
            'member_id' => $member->id,
            'payment_month' => '2026-06-01',
            'due_date' => '2026-06-10',
            'share_value' => 1000,
            'share_cost' => 50,
            'total_amount' => 1050,
            'amount_paid' => 1050,
            'payment_status_detail' => 'full',
            'payment_method' => 'cash',
            'transaction_no' => 'PAY-CHK-001',
            'status' => 'approved',
            'receipt_no' => 'RCPT-CHK-001',
        ]);

        $this->actingAs($user)
            ->post(route('checkout-requests.store'), [
                'checkout_type' => 'partial',
                'partial_percentage' => 50,
                'outstanding_loan_deducted' => 100,
            ])
            ->assertRedirect(route('checkout-requests.index'));

        $request = CheckoutRequest::query()->firstOrFail();

        $this->assertSame('partial', $request->checkout_type);
        $this->assertSame('425.00', (string) $request->refundable_amount);
        $this->assertSame('pending', $request->status);

        Notification::assertSentTo($user, MemberCheckoutRequestNotification::class);
    }

    public function test_auto_approve_payments_setting_approves_new_payments_and_notifies_member(): void
    {
        Notification::fake();

        $settings = app(SettingsService::class);
        $settings->put('auto_approve_payments', true, 'bool');
        $settings->put('email_enabled', true, 'bool');
        $settings->put('notification_channels', true, 'bool');

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['role' => 'member']);
        $member = Member::factory()->create([
            'user_id' => $user->id,
            'share_number' => 2,
        ]);

        ShareSetting::query()->create([
            'share_value' => 1000,
            'share_cost' => 50,
            'effective_from' => '2026-06-01',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.store'), [
                'member_id' => $member->id,
                'payment_month' => now()->startOfMonth()->toDateString(),
                'due_date' => now()->startOfMonth()->addDays(10)->toDateString(),
                'amount_paid' => '',
                'payment_method' => 'cash',
                'status' => 'pending',
                'payment_status_detail' => 'full',
            ])
            ->assertRedirect();

        $payment = Payment::query()->firstOrFail();

        $this->assertSame('approved', $payment->status);
        $this->assertNotNull($payment->approved_at);
        $this->assertSame($admin->id, $payment->approved_by);

        Notification::assertSentTo($user, MemberPaymentNotification::class);
    }

    public function test_reports_and_receipts_can_be_exported(): void
    {
        $admin = User::factory()->admin()->create();
        $member = Member::factory()->create();
        $category = ExpenseCategory::query()->create([
            'name' => 'Operations',
            'description' => 'Operational cost',
            'status' => 'active',
        ]);

        Project::query()->create([
            'name' => 'Warehouse Expansion',
            'description' => 'Capital project',
            'invested_amount' => 50000,
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        $payment = Payment::query()->create([
            'member_id' => $member->id,
            'payment_month' => '2026-06-01',
            'due_date' => '2026-06-10',
            'share_value' => 5000,
            'share_cost' => 250,
            'total_amount' => 5250,
            'amount_paid' => 7000,
            'payment_status_detail' => 'full',
            'payment_method' => 'cash',
            'transaction_no' => 'PAY-REPORT-001',
            'status' => 'approved',
            'receipt_no' => 'RCPT-REPORT-001',
        ]);

        Expense::query()->create([
            'expense_category_id' => $category->id,
            'title' => 'Office rent',
            'description' => 'Monthly rent',
            'amount' => 1200,
            'date' => '2026-06-15',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $reportResponse = $this->actingAs($admin)->get(route('admin.reports.export', [
            'from' => '2026-06-01',
            'to' => '2026-06-30',
        ]));

        $reportResponse->assertOk();
        $this->assertStringContainsString('Type', $reportResponse->streamedContent());
        $this->assertStringContainsString('payment', $reportResponse->streamedContent());
        $this->assertStringContainsString('expense', $reportResponse->streamedContent());
        $this->assertStringContainsString('project_investment', $reportResponse->streamedContent());

        $receiptResponse = $this->actingAs($admin)->get(route('admin.payments.receipt.download', $payment));
        $receiptResponse->assertOk();
        $this->assertStringContainsString('Receipt No', $receiptResponse->streamedContent());
        $this->assertStringContainsString('RCPT-REPORT-001', $receiptResponse->streamedContent());
    }
}
