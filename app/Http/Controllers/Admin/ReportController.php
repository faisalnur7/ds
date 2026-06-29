<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403, 'You do not have permission to access this area.');

        [$from, $to, $transactions] = $this->transactionsFor($request);

        $payments = $transactions->where('transaction_type', 'payment')->values();
        $expenses = $transactions->where('transaction_type', 'expense')->values();
        $projectInvestments = $transactions->where('transaction_type', 'project_investment')->values();
        $shareCollections = $payments->sum('amount');
        $expenseTotal = $expenses->sum('amount');

        return view('admin.reports.index', [
            'filters' => [
                'from' => $from?->toDateString(),
                'to' => $to?->toDateString(),
            ],
            'summary' => [
                'payments' => $shareCollections,
                'expenses' => $expenseTotal,
                'netShareCost' => $shareCollections - $expenseTotal,
                'projectInvestments' => $projectInvestments->sum('amount'),
                'netCashFlow' => $shareCollections - $expenseTotal - $projectInvestments->sum('amount'),
                'transactions' => $transactions->count(),
            ],
            'payments' => $payments,
            'expenses' => $expenses,
            'projectInvestments' => $projectInvestments,
            'monthlyRows' => $this->buildMonthlyRows($transactions),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        abort_unless($request->user()?->isAdmin(), 403, 'You do not have permission to access this area.');

        [$from, $to, $transactions] = $this->transactionsFor($request);
        $filename = sprintf(
            'transactions-%s-%s.csv',
            $from?->format('Ymd') ?? 'all',
            $to?->format('Ymd') ?? 'all'
        );

        return response()->streamDownload(function () use ($transactions): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Type', 'Direction', 'Reference', 'Date', 'Amount', 'Status', 'Member', 'Project', 'Category', 'Description']);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction->transaction_type,
                    $transaction->direction,
                    $transaction->reference_no,
                    optional($transaction->transaction_date)->format('Y-m-d'),
                    number_format((float) $transaction->amount, 2, '.', ''),
                    $transaction->status,
                    $transaction->member?->full_name ?? $transaction->member?->member_code ?? '',
                    $transaction->project?->name ?? '',
                    $transaction->expenseCategory?->name ?? '',
                    $transaction->description ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function buildMonthlyRows(Collection $transactions): Collection
    {
        return $transactions
            ->groupBy(fn (Transaction $transaction): string => $transaction->transaction_date?->format('Y-m') ?? 'unknown')
            ->sortKeys()
            ->map(function (Collection $group, string $month): array {
                $payments = $group->where('transaction_type', 'payment')->sum('amount');
                $expenses = $group->where('transaction_type', 'expense')->sum('amount');
                $investments = $group->where('transaction_type', 'project_investment')->sum('amount');

                return [
                    'month' => $month === 'unknown' ? 'Unknown' : Carbon::parse($month.'-01')->format('M Y'),
                    'payments' => $payments,
                    'expenses' => $expenses,
                    'netShareCost' => $payments - $expenses,
                    'projectInvestments' => $investments,
                    'netCashFlow' => $payments - $expenses - $investments,
                ];
            })
            ->values();
    }

    /**
     * @return array{0:?Carbon,1:?Carbon,2:\Illuminate\Support\Collection<int, Transaction>}
     */
    private function transactionsFor(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->string('from')->toString())->startOfDay() : null;
        $to = $request->filled('to') ? Carbon::parse($request->string('to')->toString())->endOfDay() : null;

        $transactions = Transaction::query()
            ->with(['member', 'project', 'expenseCategory'])
            ->when($from, fn ($query) => $query->whereDate('transaction_date', '>=', $from->toDateString()))
            ->when($to, fn ($query) => $query->whereDate('transaction_date', '<=', $to->toDateString()))
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        return [$from, $to, $transactions];
    }
}
