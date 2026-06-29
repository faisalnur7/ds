<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Payment;
use App\Models\ShareSetting;
use App\Notifications\MemberPaymentNotification;
use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends CrudController
{
    protected function modelClass(): string
    {
        return Payment::class;
    }

    protected function title(): string
    {
        return 'Payments';
    }

    protected function viewPrefix(): string
    {
        return 'payments';
    }

    protected function routeParameter(): string
    {
        return 'payment';
    }

    protected function pageDescription(): string
    {
        return 'Payment capture, approval state, and receipt tracking. Share values are filled from the selected member using the member share count, and reference numbers are generated automatically.';
    }

    protected function formContainerClass(): string
    {
        return 'mx-auto max-w-7xl space-y-6';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Member', 'key' => 'member.member_code'],
            ['label' => 'Month', 'key' => 'payment_month'],
            ['label' => 'Paid', 'key' => 'amount_paid', 'type' => 'money'],
            ['label' => 'Status', 'key' => 'status'],
        ];
    }

    protected function with(): array
    {
        return ['member'];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'member_id', 'label' => 'Member', 'type' => 'select', 'options' => ['' => 'Select member'] + Member::query()->pluck('member_code', 'id')->all()],
            ['name' => 'payment_month', 'label' => 'Payment Month', 'type' => 'date'],
            ['name' => 'share_value', 'label' => 'Share Value', 'type' => 'number', 'readonly' => true, 'help' => 'Auto-filled from the selected member share count.'],
            ['name' => 'share_cost', 'label' => 'Share Cost', 'type' => 'number', 'readonly' => true, 'help' => 'Auto-filled from the selected member share count.'],
            ['name' => 'amount_paid', 'label' => 'Amount Paid', 'type' => 'number'],
            ['name' => 'payment_method', 'label' => 'Payment Method', 'type' => 'select', 'options' => ['cash' => 'Cash', 'bank' => 'Bank', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'other' => 'Other']],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']],
            ['name' => 'payment_status_detail', 'label' => 'Payment Status Detail', 'type' => 'select', 'options' => ['full' => 'Full', 'partial' => 'Partial']],
        ];
    }

    public function create(): View
    {
        $model = $this->modelClass();
        $record = new $model();
        $this->requirePermission(request(), 'create');
        $this->applyShareDefaults($record);

        return view('admin.crud.form', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'fields' => $this->formFields($record),
            'record' => $record,
            'memberDefaults' => $this->memberDefaultsMap(),
            'memberPaymentMonths' => $this->memberPaymentMonthsMap(),
            'paymentAutoFill' => true,
            'routePrefix' => $this->viewPrefix(),
            'action' => route("admin.{$this->viewPrefix()}.store"),
            'method' => 'POST',
            'submitLabel' => 'Create',
            'canSubmit' => request()->user()?->hasPermission($this->permissionFor('create')) ?? false,
        ]);
    }

    public function edit(Request $request): View
    {
        $this->requirePermission($request, 'edit');

        $record = $this->resolveRecord($request);

        return view('admin.crud.form', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'fields' => $this->formFields($record),
            'record' => $record,
            'memberDefaults' => $this->memberDefaultsMap(),
            'memberPaymentMonths' => $this->memberPaymentMonthsMap(),
            'paymentAutoFill' => false,
            'routePrefix' => $this->viewPrefix(),
            'action' => route("admin.{$this->viewPrefix()}.update", $record),
            'method' => 'PUT',
            'submitLabel' => 'Update',
            'canSubmit' => $request->user()?->hasPermission($this->permissionFor('update')) ?? false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = validator(
            $this->mergeDefaults($request->all()),
            $this->rules(),
            [
            'payment_month.unique' => __('Payment is already done for the selected month.'),
            ]
        )->validate();

        $data = $this->transformInput($data);

        $model = $this->modelClass();
        $record = $model::create($data);
        $this->afterSave($record, $data, $request);

        return redirect()->route("admin.{$this->viewPrefix()}.receipt", $record)->with('status', 'created');
    }

    public function update(Request $request): RedirectResponse
    {
        $record = $this->resolveRecord($request);
        $data = validator(
            $this->mergeDefaults($request->all(), $record),
            $this->rules($record),
            [
            'payment_month.unique' => __('Payment is already done for the selected month.'),
            ]
        )->validate();

        $data = $this->transformInput($data, $record);

        $record->update($data);
        $this->afterSave($record, $data, $request);

        return redirect()->route("admin.{$this->viewPrefix()}.show", $record)->with('status', 'updated');
    }

    public function receipt(Request $request): View
    {
        $this->requirePermission($request, 'view');

        $record = $this->resolveRecord($request);

        return view('admin.payments.receipt', [
            'title' => 'Payment Receipt',
            'record' => $record,
        ]);
    }

    public function downloadReceipt(Request $request): StreamedResponse
    {
        $this->requirePermission($request, 'view');

        $record = $this->resolveRecord($request);
        $filename = sprintf('receipt-%s.csv', $record->receipt_no ?: $record->id);

        return response()->streamDownload(function () use ($record): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Field', 'Value']);
            fputcsv($handle, ['Receipt No', $record->receipt_no]);
            fputcsv($handle, ['Transaction No', $record->transaction_no]);
            fputcsv($handle, ['Member', $record->member?->full_name ?? $record->member?->member_code ?? '']);
            fputcsv($handle, ['Member Code', $record->member?->member_code ?? '']);
            fputcsv($handle, ['Payment Month', optional($record->payment_month)->format('Y-m-d')]);
            fputcsv($handle, ['Due Date', optional($record->due_date)->format('Y-m-d')]);
            fputcsv($handle, ['Share Value', number_format((float) $record->share_value, 2, '.', '')]);
            fputcsv($handle, ['Share Cost', number_format((float) $record->share_cost, 2, '.', '')]);
            fputcsv($handle, ['Total Amount', number_format((float) $record->total_amount, 2, '.', '')]);
            fputcsv($handle, ['Amount Paid', number_format((float) $record->amount_paid, 2, '.', '')]);
            fputcsv($handle, ['Method', $record->payment_method]);
            fputcsv($handle, ['Status', $record->status]);

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'payment_month' => [
                'required',
                'date',
                function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                    $query = Payment::query()
                        ->where('member_id', request('member_id'))
                        ->whereDate('payment_month', $value);

                    if ($record) {
                        $query->whereKeyNot($record->getKey());
                    }

                    if ($query->exists()) {
                        $fail(__('Payment is already done for the selected month.'));
                    }
                },
            ],
            'share_value' => ['required', 'numeric', 'min:0'],
            'share_cost' => ['required', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,bank,bkash,nagad,rocket,other'],
            'transaction_no' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'receipt_no' => ['nullable', 'string', 'max:255'],
            'payment_status_detail' => ['required', 'in:full,partial'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        $memberDefaults = $this->memberDefaults((int) ($input['member_id'] ?? $record?->member_id));
        $settings = app(SettingsService::class);

        $input['share_value'] = $memberDefaults['share_value'];
        $input['share_cost'] = $memberDefaults['share_cost'];
        $input['total_amount'] = $memberDefaults['share_value'] + $memberDefaults['share_cost'];
        $input['amount_paid'] = filled($input['amount_paid'] ?? null) ? $input['amount_paid'] : $input['total_amount'];
        $input['transaction_no'] = $record?->transaction_no ?: $this->generateReference('PAY');
        $input['receipt_no'] = $record?->receipt_no ?: $this->generateReference('RCPT', 'receipt_no');

        if ($settings->get('auto_approve_payments', false) && ($input['status'] ?? 'pending') !== 'rejected') {
            $input['status'] = 'approved';
            $input['approved_at'] = $input['approved_at'] ?? now();
            $input['approved_by'] = $input['approved_by'] ?? request()->user()?->id;
        } elseif (($input['status'] ?? null) === 'approved') {
            $input['approved_at'] = $input['approved_at'] ?? now();
            $input['approved_by'] = $input['approved_by'] ?? request()->user()?->id;
        }

        return $input;
    }

    protected function applyShareDefaults(Payment $payment, ?int $memberId = null): void
    {
        $defaults = $this->memberDefaults($memberId);

        $payment->fill([
            'payment_month' => now()->startOfMonth()->toDateString(),
            'share_value' => $defaults['share_value'],
            'share_cost' => $defaults['share_cost'],
            'amount_paid' => $defaults['share_value'] + $defaults['share_cost'],
            'payment_status_detail' => 'full',
            'payment_method' => 'cash',
            'status' => 'pending',
        ]);
    }

    protected function memberDefaults(?int $memberId = null): array
    {
        $shareSetting = ShareSetting::current();

        if ($memberId) {
            $member = Member::query()->find($memberId);

            if ($member) {
                $shareNumber = max(1, (int) ($member->share_number ?: 1));

                return [
                    'share_number' => $shareNumber,
                    'share_value' => (float) ($shareSetting?->share_value ?? 0) * $shareNumber,
                    'share_cost' => (float) ($shareSetting?->share_cost ?? 0) * $shareNumber,
                ];
            }
        }

        return [
            'share_number' => 1,
            'share_value' => (float) ($shareSetting?->share_value ?? 0),
            'share_cost' => (float) ($shareSetting?->share_cost ?? 0),
        ];
    }

    protected function mergeDefaults(array $input, ?Model $record = null): array
    {
        $defaults = $this->memberDefaults((int) ($input['member_id'] ?? $record?->member_id));

        return array_merge($input, [
            'share_value' => $defaults['share_value'],
            'share_cost' => $defaults['share_cost'],
            'total_amount' => $defaults['share_value'] + $defaults['share_cost'],
            'payment_month' => $input['payment_month'] ?? now()->startOfMonth()->toDateString(),
            'payment_method' => $input['payment_method'] ?? 'cash',
            'status' => $input['status'] ?? 'pending',
            'payment_status_detail' => $input['payment_status_detail'] ?? 'full',
            'amount_paid' => filled($input['amount_paid'] ?? null) ? $input['amount_paid'] : ($defaults['share_value'] + $defaults['share_cost']),
        ]);
    }

    protected function memberDefaultsMap(): array
    {
        $map = Member::query()
            ->get()
            ->mapWithKeys(function (Member $member): array {
                return [
                    (string) $member->id => $this->memberDefaults($member->getKey()),
                ];
            })
            ->all();

        $map['__default'] = $this->memberDefaults();

        return $map;
    }

    protected function memberPaymentMonthsMap(): array
    {
        return Member::query()
            ->with(['payments:id,member_id,payment_month'])
            ->get()
            ->mapWithKeys(function (Member $member): array {
                return [
                    (string) $member->id => $member->payments
                        ->map(fn (Payment $payment) => optional($payment->payment_month)->format('Y-m-d'))
                        ->filter()
                        ->values()
                        ->all(),
                ];
            })
            ->all();
    }

    protected function generateReference(string $prefix, string $column = 'transaction_no'): string
    {
        do {
            $reference = sprintf('%s-%s-%s', $prefix, now()->format('Ymd'), Str::upper(Str::random(6)));
        } while (Payment::query()->where($column, $reference)->exists());

        return $reference;
    }

    protected function afterSave(Model $record, array $data, Request $request): void
    {
        $memberUser = $record->member?->user;

        if (! $memberUser) {
            return;
        }

        $changes = $record->getChanges();
        $shouldNotify = $record->wasRecentlyCreated || ! empty($changes);

        if (! $shouldNotify) {
            return;
        }

        $notification = match ($record->status) {
            'approved' => MemberPaymentNotification::approved($record),
            'rejected' => MemberPaymentNotification::rejected($record),
            default => MemberPaymentNotification::created($record),
        };

        $memberUser->notify($notification);
    }
}
