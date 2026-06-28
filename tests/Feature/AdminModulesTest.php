<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Member;
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
        $this->actingAs($admin)->get(route('admin.loans.index'))->assertOk();
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

    public function test_admin_loans_show_borrower_name_and_full_details(): void
    {
        $admin = User::factory()->admin()->create();
        $member = Member::factory()->create([
            'full_name' => 'Borrower Example',
            'member_code' => 'DS-8888',
            'phone' => '+8801700008888',
            'father_name' => 'Father Example',
            'mother_name' => 'Mother Example',
            'occupation' => 'Farmer',
        ]);
        $loan = Loan::query()->create([
            'member_id' => $member->id,
            'principal_amount' => 12500,
            'tenure_months' => 12,
            'status' => 'active',
            'approved_by' => $admin->id,
            'disbursed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.loans.index'))
            ->assertOk()
            ->assertSee('Borrower Example')
            ->assertSee('DS-8888');

        $this->actingAs($admin)
            ->get(route('admin.loans.show', $loan))
            ->assertOk()
            ->assertSee('Borrower Example')
            ->assertSee('DS-8888')
            ->assertSee('Farmer')
            ->assertSee('No repayment rows have been recorded yet.');
    }

    public function test_member_is_blocked_from_admin_pages(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.members.index'))->assertForbidden();
    }
}
