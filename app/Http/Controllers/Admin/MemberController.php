<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\MemberShareHistory;
use App\Models\ShareSetting;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends CrudController
{
    protected function modelClass(): string
    {
        return Member::class;
    }

    protected function title(): string
    {
        return 'Members';
    }

    protected function viewPrefix(): string
    {
        return 'members';
    }

    protected function routeParameter(): string
    {
        return 'member';
    }

    protected function pageDescription(): string
    {
        return 'Member onboarding, KYC, and lifecycle records.';
    }

    public function index(Request $request): View
    {
        $this->requirePermission($request, 'view');

        $query = Member::query()->with('user');

        if ($search = trim((string) $request->string('q'))) {
            $searchDigits = preg_replace('/\D+/', '', $search);

            $query->where(function ($builder) use ($search, $searchDigits): void {
                $builder->where('member_code', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('phone_search', 'like', "%{$search}%");

                if ($searchDigits !== '' && $searchDigits !== $search) {
                    $builder->orWhere('phone_search', 'like', "%{$searchDigits}%");
                }

                if (is_numeric($search)) {
                    $builder->orWhere('id', (int) $search);
                }
            });
        }

        if ($jimmadarId = $request->filled('jimmadar_id') ? $request->integer('jimmadar_id') : null) {
            $query->where('user_id', $jimmadarId);
        }

        if ($joinFrom = $request->filled('join_from') ? $request->date('join_from') : null) {
            $query->whereDate('join_date', '>=', $joinFrom);
        }

        if ($joinTo = $request->filled('join_to') ? $request->date('join_to') : null) {
            $query->whereDate('join_date', '<=', $joinTo);
        }

        $members = $query->latest('join_date')->latest('id')->paginate(10)->withQueryString();

        return view('admin.members.index', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'members' => $members,
            'jimmadars' => User::query()->orderBy('name')->pluck('name', 'id')->all(),
            'filters' => $request->only(['q', 'jimmadar_id', 'join_from', 'join_to']),
        ]);
    }

    public function create(): View
    {
        $request = request();
        $this->requirePermission($request, 'create');

        return view('admin.members.create', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'memberCode' => Member::nextMemberCode(),
            'action' => route('admin.members.store'),
            'backUrl' => route('admin.members.index'),
        ]);
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Code', 'key' => 'member_code'],
            ['label' => 'Name', 'key' => 'full_name'],
            ['label' => 'Status', 'key' => 'membership_status'],
            ['label' => 'Join Date', 'key' => 'join_date'],
        ];
    }

    protected function with(): array
    {
        return ['user'];
    }

    public function store(Request $request): RedirectResponse
    {
        $this->requirePermission($request, 'create');

        $data = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'spouse_phone' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'religion' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'nid_number' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'present_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'nominee_name' => ['nullable', 'string', 'max:255'],
            'nominee_relation' => ['nullable', 'string', 'max:255'],
            'nominee_phone' => ['nullable', 'string', 'max:255'],
            'reference_name' => ['nullable', 'string', 'max:255'],
            'reference_phone' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'join_date' => ['required', 'date'],
            'share_number' => ['required', 'integer', 'min:1', 'max:' . max(1, (int) app(SettingsService::class)->get('maximum_share_per_member', 10))],
            'membership_status' => ['required', 'in:active,revoked,checked_out'],
        ]);

        $record = DB::transaction(function () use ($request, $data): Member {
            $user = User::create([
                'name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'member',
                'status' => 'active',
            ]);

            $record = Member::create([
                'user_id' => $user->id,
                'member_code' => Member::nextMemberCode(),
                'full_name' => $data['full_name'],
                'father_name' => $data['father_name'] ?? null,
                'mother_name' => $data['mother_name'] ?? null,
                'spouse_name' => $data['spouse_name'] ?? null,
                'spouse_phone' => $data['spouse_phone'] ?? null,
                'blood_group' => $data['blood_group'] ?? null,
                'religion' => $data['religion'] ?? null,
                'education' => $data['education'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'phone' => $data['phone'],
                'phone_search' => preg_replace('/\D+/', '', (string) $data['phone']) ?: null,
                'nid_number' => $data['nid_number'],
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'present_address' => $data['present_address'] ?? null,
                'permanent_address' => $data['permanent_address'] ?? null,
                'nominee_name' => $data['nominee_name'] ?? null,
                'nominee_relation' => $data['nominee_relation'] ?? null,
                'nominee_phone' => $data['nominee_phone'] ?? null,
                'reference_name' => $data['reference_name'] ?? null,
                'reference_phone' => $data['reference_phone'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'join_date' => $data['join_date'],
                'share_number' => $data['share_number'],
                'membership_status' => $data['membership_status'],
            ]);

            $this->recordShareHistory($record, null, (int) $record->share_number, $request->user()?->id, 'Member record created.');

            return $record;
        });

        return redirect()->route("admin.{$this->viewPrefix()}.show", $record)->with('status', 'created');
    }

    public function update(Request $request): RedirectResponse
    {
        $this->requirePermission($request, 'update');

        $record = $this->resolveRecord($request);
        $originalShareNumber = (int) $record->share_number;
        $data = $request->validate($this->rules($record));
        $data = $this->transformInput($data, $record);

        $record->update($data);

        $newShareNumber = (int) $record->share_number;

        if ($originalShareNumber !== $newShareNumber) {
            $note = sprintf('Share count changed from %d to %d.', $originalShareNumber, $newShareNumber);
            $this->recordShareHistory($record, $originalShareNumber, $newShareNumber, $request->user()?->id, $note);
        }

        return redirect()->route("admin.{$this->viewPrefix()}.show", $record)->with('status', 'updated');
    }

    protected function formFields(?Model $record = null): array
    {
        $checkoutEligibleMonths = (int) app(SettingsService::class)->get('checkout_eligible_months', 12);
        $maximumSharePerMember = max(1, (int) app(SettingsService::class)->get('maximum_share_per_member', 10));

        return [
            ['name' => 'user_id', 'label' => 'Jimmadar', 'type' => 'select', 'options' => User::query()->pluck('name', 'id')->all()],
            ['name' => 'member_code', 'label' => 'Member Code', 'type' => 'text'],
            ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'text'],
            ['name' => 'father_name', 'label' => 'Father Name', 'type' => 'text'],
            ['name' => 'mother_name', 'label' => 'Mother Name', 'type' => 'text'],
            ['name' => 'spouse_name', 'label' => 'Spouse Name', 'type' => 'text'],
            ['name' => 'spouse_phone', 'label' => 'Spouse Phone', 'type' => 'text'],
            ['name' => 'blood_group', 'label' => 'Blood Group', 'type' => 'select', 'options' => [
                '' => 'Select blood group',
                'A+' => 'A+',
                'A-' => 'A-',
                'B+' => 'B+',
                'B-' => 'B-',
                'AB+' => 'AB+',
                'AB-' => 'AB-',
                'O+' => 'O+',
                'O-' => 'O-',
            ]],
            ['name' => 'religion', 'label' => 'Religion', 'type' => 'text'],
            ['name' => 'education', 'label' => 'Education', 'type' => 'text'],
            ['name' => 'emergency_contact_name', 'label' => 'Emergency Contact Name', 'type' => 'text'],
            ['name' => 'emergency_contact_phone', 'label' => 'Emergency Contact Phone', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['name' => 'nid_number', 'label' => 'NID Number', 'type' => 'text'],
            ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'],
            ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
            ['name' => 'present_address', 'label' => 'Present Address', 'type' => 'textarea', 'span' => 2],
            ['name' => 'permanent_address', 'label' => 'Permanent Address', 'type' => 'textarea', 'span' => 2],
            ['name' => 'nominee_name', 'label' => 'Nominee Name', 'type' => 'text'],
            ['name' => 'nominee_relation', 'label' => 'Nominee Relation', 'type' => 'text'],
            ['name' => 'nominee_phone', 'label' => 'Nominee Phone', 'type' => 'text'],
            ['name' => 'reference_name', 'label' => 'Reference Name', 'type' => 'text'],
            ['name' => 'reference_phone', 'label' => 'Reference Phone', 'type' => 'text'],
            ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'textarea', 'span' => 2],
            ['name' => 'join_date', 'label' => 'Join Date', 'type' => 'date'],
            ['name' => 'share_number', 'label' => 'Share Number', 'type' => 'number', 'min' => 1, 'max' => $maximumSharePerMember, 'step' => 1, 'help' => "Number of shares held by the member. Maximum allowed is {$maximumSharePerMember}."],
            ['name' => 'checkout_eligible_on', 'label' => 'Checkout Eligible On', 'type' => 'computed-date', 'help' => 'Calculated from join date and the global checkout eligibility setting.', 'months_value' => $checkoutEligibleMonths, 'span' => 2],
            ['name' => 'membership_status', 'label' => 'Membership Status', 'type' => 'select', 'options' => ['active' => 'Active', 'revoked' => 'Revoked', 'checked_out' => 'Checked Out']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        $maximumSharePerMember = max(1, (int) app(SettingsService::class)->get('maximum_share_per_member', 10));

        return [
            'user_id' => ['required', 'exists:users,id', Rule::unique('members', 'user_id')->ignore($record?->getKey())],
            'member_code' => ['required', 'string', 'max:255', Rule::unique('members', 'member_code')->ignore($record?->getKey())],
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'spouse_phone' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'religion' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'nid_number' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'present_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'nominee_name' => ['nullable', 'string', 'max:255'],
            'nominee_relation' => ['nullable', 'string', 'max:255'],
            'nominee_phone' => ['nullable', 'string', 'max:255'],
            'reference_name' => ['nullable', 'string', 'max:255'],
            'reference_phone' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'join_date' => ['required', 'date'],
            'share_number' => ['required', 'integer', 'min:1', 'max:'.$maximumSharePerMember],
            'membership_status' => ['required', 'in:active,revoked,checked_out'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        $input['phone_search'] = preg_replace('/\D+/', '', (string) ($input['phone'] ?? ''));

        if ($input['phone_search'] === '') {
            $input['phone_search'] = null;
        }

        return $input;
    }

    protected function showContext(Model $record): array
    {
        /** @var Member $record */
        return [
            'share_history' => $record->shareHistories()
                ->with('changedBy')
                ->latest('changed_at')
                ->get(),
        ];
    }

    protected function recordShareHistory(Member $member, ?int $previousShareNumber, int $shareNumber, ?int $changedBy, ?string $note = null): void
    {
        $shareSetting = ShareSetting::current();

        MemberShareHistory::query()->create([
            'member_id' => $member->id,
            'changed_by' => $changedBy,
            'previous_share_number' => $previousShareNumber,
            'share_number' => $shareNumber,
            'share_value_per_share' => $shareSetting?->share_value,
            'share_cost_per_share' => $shareSetting?->share_cost,
            'monthly_amount' => $shareSetting ? ((float) $shareSetting->share_value + (float) $shareSetting->share_cost) * $shareNumber : null,
            'changed_at' => Carbon::now(),
            'note' => $note,
        ]);
    }
}
