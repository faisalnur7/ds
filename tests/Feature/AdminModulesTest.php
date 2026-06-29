<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_module_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->hasPermission('view_expense_menu'));
        $this->actingAs($admin)->get(route('admin.members.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.payments.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.expense-categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.expenses.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.projects.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.checkout-requests.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.settings.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.audit.index'))->assertOk();
    }

    public function test_cashier_role_can_manage_expense_pages_and_approve_expenses(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);
        $category = ExpenseCategory::query()->create([
            'name' => 'Stationery',
            'description' => 'Office supplies',
            'status' => 'active',
        ]);
        $expense = Expense::query()->create([
            'expense_category_id' => $category->id,
            'title' => 'Printer paper',
            'description' => 'A4 paper for office use',
            'amount' => 1200,
            'date' => '2026-06-28',
            'status' => 'pending',
        ]);

        $this->assertTrue($cashier->hasPermission('view_expenses'));
        $this->assertTrue($cashier->hasPermission('approve_expenses'));

        $this->actingAs($cashier)->get(route('admin.expense-categories.index'))->assertOk();
        $this->actingAs($cashier)->get(route('admin.expenses.index'))->assertOk();

        $this->actingAs($cashier)
            ->patch(route('admin.expenses.approve', $expense))
            ->assertRedirect(route('admin.expenses.show', $expense));

        $expense->refresh();

        $this->assertSame('approved', $expense->status);
        $this->assertSame($cashier->id, $expense->approved_by);
        $this->assertNotNull($expense->approved_at);
    }

    public function test_transactions_are_recorded_for_payments_expenses_and_projects(): void
    {
        $admin = User::factory()->admin()->create();
        $member = Member::factory()->create(['full_name' => 'Ledger Member']);
        $category = ExpenseCategory::query()->create([
            'name' => 'Utilities',
            'description' => 'Monthly utilities',
            'status' => 'active',
        ]);

        Project::query()->create([
            'name' => 'Warehouse Expansion',
            'description' => 'Capital project',
            'invested_amount' => 50000,
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        Payment::query()->create([
            'member_id' => $member->id,
            'payment_month' => '2026-06-01',
            'share_value' => 5000,
            'share_cost' => 250,
            'total_amount' => 5250,
            'amount_paid' => 5250,
            'payment_status_detail' => 'full',
            'payment_method' => 'cash',
            'transaction_no' => 'PAY-LEDGER-001',
            'status' => 'approved',
            'receipt_no' => 'RCPT-LEDGER-001',
            'created_by' => $admin->id,
        ]);

        Expense::query()->create([
            'expense_category_id' => $category->id,
            'title' => 'Generator fuel',
            'description' => 'Fuel purchase for backup generator',
            'amount' => 1200,
            'date' => '2026-06-15',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $this->assertSame(3, Transaction::query()->count());
        $this->assertSame('payment', Transaction::query()->where('source_type', Payment::class)->value('transaction_type'));
        $this->assertSame('expense', Transaction::query()->where('source_type', Expense::class)->value('transaction_type'));
        $this->assertSame('project_investment', Transaction::query()->where('source_type', Project::class)->value('transaction_type'));
    }

    public function test_admin_reports_page_aggregates_ledger_totals(): void
    {
        $admin = User::factory()->admin()->create();
        $member = Member::factory()->create(['full_name' => 'Report Member']);
        $category = ExpenseCategory::query()->create([
            'name' => 'Operations',
            'description' => 'Operational cost',
            'status' => 'active',
        ]);

        Project::query()->create([
            'name' => 'Community Hall',
            'description' => 'Project funding',
            'invested_amount' => 50000,
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);

        Payment::query()->create([
            'member_id' => $member->id,
            'payment_month' => '2026-06-01',
            'share_value' => 5000,
            'share_cost' => 250,
            'total_amount' => 5250,
            'amount_paid' => 7000,
            'payment_status_detail' => 'full',
            'payment_method' => 'cash',
            'transaction_no' => 'PAY-REPORT-001',
            'status' => 'approved',
            'receipt_no' => 'RCPT-REPORT-001',
            'created_by' => $admin->id,
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

        $this->actingAs($admin)
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Project investments')
            ->assertSee('Payments')
            ->assertSee('Expenses')
            ->assertSee('Share cost after expenses')
            ->assertSee('50,000.00')
            ->assertSee('7,000.00')
            ->assertSee('1,200.00')
            ->assertSee('5,800.00')
            ->assertSee('-44,200.00')
            ->assertSee(route('admin.expenses.index'), false)
            ->assertSee('Jun 2026');
    }

    public function test_member_is_blocked_from_admin_pages(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.members.index'))->assertForbidden();
    }
}
