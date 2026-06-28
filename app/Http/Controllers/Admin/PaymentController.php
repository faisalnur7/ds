<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Payment;
use App\Models\ShareSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

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
            ['name' => 'due_date', 'label' => 'Due Date', 'type' => 'date'],
            ['name' => 'share_value', 'label' => 'Share Value', 'type' => 'number', 'readonly' => true, 'help' => 'Auto-filled from the selected member share count.'],
            ['name' => 'share_cost', 'label' => 'Share Cost', 'type' => 'number', 'readonly' => true, 'help' => 'Auto-filled from the selected member share count.'],
            ['name' => 'fine_amount', 'label' => 'Fine', 'type' => 'number', 'readonly' => true, 'help' => 'Auto-filled from the active share settings.'],
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
        $data = $this->transformInput(
            $this->mergeDefaults($request->all())
        );

        $data = validator($data, $this->rules())->validate();

        $model = $this->modelClass();
        $record = $model::create($data);
        $this->afterSave($record, $data, $request);

        return redirect()->route("admin.{$this->viewPrefix()}.show", $record)->with('status', 'created');
    }

    public function update(Request $request): RedirectResponse
    {
        $record = $this->resolveRecord($request);
        $data = $this->transformInput(
            $this->mergeDefaults($request->all(), $record)
        , $record);

        $data = validator($data, $this->rules($record))->validate();

        $record->update($data);
        $this->afterSave($record, $data, $request);

        return redirect()->route("admin.{$this->viewPrefix()}.show", $record)->with('status', 'updated');
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'payment_month' => [
                'required',
                'date',
                Rule::unique('payments', 'payment_month')->where(fn ($query) => $query->where('member_id', request('member_id')))->ignore($record?->getKey()),
            ],
            'due_date' => ['required', 'date'],
            'share_value' => ['required', 'numeric', 'min:0'],
            'share_cost' => ['required', 'numeric', 'min:0'],
            'fine_amount' => ['required', 'numeric', 'min:0'],
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

        $input['share_value'] = $memberDefaults['share_value'];
        $input['share_cost'] = $memberDefaults['share_cost'];
        $input['fine_amount'] = $memberDefaults['fine_amount'];
        $input['total_amount'] = $memberDefaults['share_value'] + $memberDefaults['share_cost'] + $memberDefaults['fine_amount'];
        $input['amount_paid'] = filled($input['amount_paid'] ?? null) ? $input['amount_paid'] : $input['total_amount'];
        $input['transaction_no'] = $record?->transaction_no ?: $this->generateReference('PAY');
        $input['receipt_no'] = $record?->receipt_no ?: $this->generateReference('RCPT', 'receipt_no');

        return $input;
    }

    protected function applyShareDefaults(Payment $payment, ?int $memberId = null): void
    {
        $defaults = $this->memberDefaults($memberId);

        $payment->fill([
            'payment_month' => now()->startOfMonth()->toDateString(),
            'due_date' => now()->startOfMonth()->addDays(10)->toDateString(),
            'share_value' => $defaults['share_value'],
            'share_cost' => $defaults['share_cost'],
            'fine_amount' => $defaults['fine_amount'],
            'amount_paid' => $defaults['share_value'] + $defaults['share_cost'] + $defaults['fine_amount'],
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
                    'fine_amount' => (float) ($shareSetting?->fine_amount ?? 0),
                ];
            }
        }

        return [
            'share_number' => 1,
            'share_value' => (float) ($shareSetting?->share_value ?? 0),
            'share_cost' => (float) ($shareSetting?->share_cost ?? 0),
            'fine_amount' => (float) ($shareSetting?->fine_amount ?? 0),
        ];
    }

    protected function mergeDefaults(array $input, ?Model $record = null): array
    {
        $defaults = $this->memberDefaults((int) ($input['member_id'] ?? $record?->member_id));

        return array_merge($input, [
            'share_value' => $defaults['share_value'],
            'share_cost' => $defaults['share_cost'],
            'fine_amount' => $defaults['fine_amount'],
            'payment_month' => $input['payment_month'] ?? now()->startOfMonth()->toDateString(),
            'due_date' => $input['due_date'] ?? now()->startOfMonth()->addDays(10)->toDateString(),
            'payment_method' => $input['payment_method'] ?? 'cash',
            'status' => $input['status'] ?? 'pending',
            'payment_status_detail' => $input['payment_status_detail'] ?? 'full',
            'amount_paid' => filled($input['amount_paid'] ?? null) ? $input['amount_paid'] : ($defaults['share_value'] + $defaults['share_cost'] + $defaults['fine_amount']),
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

    protected function generateReference(string $prefix, string $column = 'transaction_no'): string
    {
        do {
            $reference = sprintf('%s-%s-%s', $prefix, now()->format('Ymd'), Str::upper(Str::random(6)));
        } while (Payment::query()->where($column, $reference)->exists());

        return $reference;
    }
}
