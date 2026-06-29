<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class TransactionLedgerService
{
    public function syncPayment(Payment $payment): Transaction
    {
        return Transaction::query()->updateOrCreate(
            ['source_type' => $payment::class, 'source_id' => $payment->getKey()],
            $this->payload(
                transactionType: 'payment',
                direction: 'in',
                amount: (float) $payment->amount_paid,
                date: $payment->payment_month?->toDateString() ?? now()->toDateString(),
                status: (string) $payment->status,
                referenceNo: $payment->transaction_no ?: $payment->receipt_no ?: sprintf('PAY-%s', $payment->getKey()),
                memberId: $payment->member_id,
                projectId: null,
                expenseCategoryId: null,
                description: "Payment for member {$payment->member_id}",
                approvedBy: $payment->approved_by,
                approvedAt: $payment->approved_at,
                createdBy: $payment->created_by,
            )
        );
    }

    public function syncExpense(Expense $expense): Transaction
    {
        return Transaction::query()->updateOrCreate(
            ['source_type' => $expense::class, 'source_id' => $expense->getKey()],
            $this->payload(
                transactionType: 'expense',
                direction: 'out',
                amount: (float) $expense->amount,
                date: $expense->date?->toDateString() ?? now()->toDateString(),
                status: (string) $expense->status,
                referenceNo: sprintf('EXP-%s', $expense->getKey()),
                memberId: null,
                projectId: null,
                expenseCategoryId: $expense->expense_category_id,
                description: $expense->title,
                approvedBy: $expense->approved_by,
                approvedAt: $expense->approved_at,
                createdBy: null,
            )
        );
    }

    public function syncProject(Project $project): Transaction
    {
        return Transaction::query()->updateOrCreate(
            ['source_type' => $project::class, 'source_id' => $project->getKey()],
            $this->payload(
                transactionType: 'project_investment',
                direction: 'out',
                amount: (float) $project->invested_amount,
                date: $project->start_date?->toDateString() ?? $project->created_at?->toDateString() ?? now()->toDateString(),
                status: $project->status === 'cancelled' ? 'rejected' : 'approved',
                referenceNo: sprintf('PRJ-%s', $project->getKey()),
                memberId: null,
                projectId: $project->getKey(),
                expenseCategoryId: null,
                description: $project->name,
                approvedBy: null,
                approvedAt: null,
                createdBy: null,
            )
        );
    }

    public function removeSource(Model $source): void
    {
        Transaction::query()
            ->where('source_type', $source::class)
            ->where('source_id', $source->getKey())
            ->delete();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(
        string $transactionType,
        string $direction,
        float $amount,
        string $date,
        string $status,
        string $referenceNo,
        ?int $memberId,
        ?int $projectId,
        ?int $expenseCategoryId,
        ?string $description,
        ?int $approvedBy,
        mixed $approvedAt,
        ?int $createdBy,
    ): array {
        return [
            'transaction_type' => $transactionType,
            'direction' => $direction,
            'amount' => $amount,
            'transaction_date' => $date,
            'status' => $status,
            'reference_no' => $referenceNo,
            'member_id' => $memberId,
            'project_id' => $projectId,
            'expense_category_id' => $expenseCategoryId,
            'description' => $description,
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
            'created_by' => $createdBy,
        ];
    }
}
