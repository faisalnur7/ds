<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->string('transaction_type')->index();
            $table->string('direction')->index();
            $table->decimal('amount', 12, 2);
            $table->date('transaction_date')->index();
            $table->string('status')->default('pending')->index();
            $table->string('reference_no')->nullable()->unique();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->string('source_type')->nullable()->index();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['transaction_type', 'transaction_date']);
            $table->index(['member_id', 'transaction_date']);
            $table->index(['project_id', 'transaction_date']);
            $table->index(['expense_category_id', 'transaction_date']);
            $table->index(['status', 'transaction_date']);
            $table->index(['source_type', 'source_id']);
        });

        $now = now();

        $paymentRows = DB::table('payments')->get();
        foreach ($paymentRows as $payment) {
            DB::table('transactions')->insert([
                'transaction_type' => 'payment',
                'direction' => 'in',
                'amount' => $payment->amount_paid,
                'transaction_date' => $payment->payment_month,
                'status' => $payment->status,
                'reference_no' => $payment->transaction_no ?: $payment->receipt_no ?: sprintf('PAY-%s', $payment->id),
                'member_id' => $payment->member_id,
                'project_id' => null,
                'expense_category_id' => null,
                'source_type' => 'App\\Models\\Payment',
                'source_id' => $payment->id,
                'description' => null,
                'approved_by' => $payment->approved_by,
                'approved_at' => $payment->approved_at,
                'created_by' => $payment->created_by,
                'created_at' => $payment->created_at ?? $now,
                'updated_at' => $payment->updated_at ?? $now,
            ]);
        }

        $expenseRows = DB::table('expenses')->whereNull('deleted_at')->get();
        foreach ($expenseRows as $expense) {
            DB::table('transactions')->insert([
                'transaction_type' => 'expense',
                'direction' => 'out',
                'amount' => $expense->amount,
                'transaction_date' => $expense->date,
                'status' => $expense->status,
                'reference_no' => sprintf('EXP-%s', $expense->id),
                'member_id' => null,
                'project_id' => null,
                'expense_category_id' => $expense->expense_category_id,
                'source_type' => 'App\\Models\\Expense',
                'source_id' => $expense->id,
                'description' => $expense->title,
                'approved_by' => $expense->approved_by,
                'approved_at' => $expense->approved_at,
                'created_by' => null,
                'created_at' => $expense->created_at ?? $now,
                'updated_at' => $expense->updated_at ?? $now,
            ]);
        }

        $projectRows = DB::table('projects')->get();
        foreach ($projectRows as $project) {
            DB::table('transactions')->insert([
                'transaction_type' => 'project_investment',
                'direction' => 'out',
                'amount' => $project->invested_amount,
                'transaction_date' => $project->start_date ?? ($project->created_at ? substr((string) $project->created_at, 0, 10) : $now->toDateString()),
                'status' => $project->status === 'cancelled' ? 'rejected' : 'approved',
                'reference_no' => sprintf('PRJ-%s', $project->id),
                'member_id' => null,
                'project_id' => $project->id,
                'expense_category_id' => null,
                'source_type' => 'App\\Models\\Project',
                'source_id' => $project->id,
                'description' => $project->name,
                'approved_by' => null,
                'approved_at' => null,
                'created_by' => null,
                'created_at' => $project->created_at ?? $now,
                'updated_at' => $project->updated_at ?? $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
