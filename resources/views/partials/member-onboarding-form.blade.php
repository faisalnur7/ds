@php
    $today = now()->toDateString();
    $memberCode = $memberCode ?? '';
    $steps = [
        [
            'title' => 'Account',
            'description' => 'Create the login credentials for the new member.',
            'fields' => [
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'autocomplete' => 'username', 'required' => true, 'span' => 2],
                ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'autocomplete' => 'new-password', 'required' => true],
                ['name' => 'password_confirmation', 'label' => 'Confirm Password', 'type' => 'password', 'autocomplete' => 'new-password', 'required' => true],
            ],
        ],
        [
            'title' => 'Identity',
            'description' => 'Capture the member identity and basic profile information.',
            'fields' => [
                ['name' => 'member_code', 'label' => 'Member Code', 'type' => 'text', 'value' => $memberCode, 'readonly' => true, 'span' => 2, 'help' => 'Generated automatically.'],
                ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date', 'max' => $today],
                ['name' => 'nid_number', 'label' => 'NID Number', 'type' => 'text', 'required' => true],
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
                ['name' => 'religion', 'label' => 'Religion', 'type' => 'select', 'options' => [
                    'Islam' => 'Islam',
                ], 'default' => 'Islam'],
                ['name' => 'education', 'label' => 'Last education degree', 'type' => 'text'],
                ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
            ],
        ],
        [
            'title' => 'Family',
            'description' => 'Add family and emergency contact details.',
            'fields' => [
                ['name' => 'father_name', 'label' => 'Father Name', 'type' => 'text'],
                ['name' => 'mother_name', 'label' => 'Mother Name', 'type' => 'text'],
                ['name' => 'spouse_name', 'label' => 'Spouse Name', 'type' => 'text'],
                ['name' => 'spouse_phone', 'label' => 'Spouse Phone', 'type' => 'text'],
                ['name' => 'phone', 'label' => 'Phone', 'type' => 'text', 'required' => true],
                ['name' => 'emergency_contact_name', 'label' => 'Emergency Contact Name', 'type' => 'text'],
                ['name' => 'emergency_contact_phone', 'label' => 'Emergency Contact Phone', 'type' => 'text'],
            ],
        ],
        [
            'title' => 'Address & Membership',
            'description' => 'Record addresses, nomination details, and membership setup.',
            'fields' => [
                ['name' => 'present_address', 'label' => 'Present Address', 'type' => 'textarea', 'span' => 2],
                ['name' => 'permanent_address', 'label' => 'Permanent Address', 'type' => 'textarea', 'span' => 2],
                ['name' => 'nominee_name', 'label' => 'Nominee Name', 'type' => 'text'],
                ['name' => 'nominee_relation', 'label' => 'Nominee Relation', 'type' => 'text'],
                ['name' => 'nominee_phone', 'label' => 'Nominee Phone', 'type' => 'text'],
                ['name' => 'reference_name', 'label' => 'Reference Name', 'type' => 'text'],
                ['name' => 'reference_phone', 'label' => 'Reference Phone', 'type' => 'text'],
                ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'textarea', 'span' => 2],
                ['name' => 'join_date', 'label' => 'Join Date', 'type' => 'date', 'required' => true, 'default' => $today],
                ['name' => 'share_number', 'label' => 'Share Number', 'type' => 'number', 'min' => 1, 'step' => 1, 'required' => true, 'default' => 1],
                ['name' => 'membership_status', 'label' => 'Membership Status', 'type' => 'select', 'required' => true, 'default' => 'active', 'options' => [
                    'active' => 'Active',
                    'revoked' => 'Revoked',
                    'checked_out' => 'Checked Out',
                ]],
            ],
        ],
    ];

    $initialStep = 1;

    foreach ($steps as $index => $step) {
        foreach ($step['fields'] as $field) {
            if ($errors->has($field['name'])) {
                $initialStep = $index + 1;
                break 2;
            }
        }
    }
@endphp

<form
    method="POST"
    action="{{ $action }}"
    class="{{ $formClass ?? 'space-y-6 rounded-[2rem] border border-white/10 bg-slate-950/70 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur sm:p-8' }}"
    x-data="memberRegistrationWizard({{ $initialStep }}, {{ count($steps) }})"
    x-init="init()"
    x-cloak
>
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="{{ $heroClass ?? 'rounded-[1.75rem] border border-white/10 bg-white/5 p-5 sm:p-6' }}">
        <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __($eyebrow ?? 'Member onboarding') }}</p>
        <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ __($heading ?? 'Register a new member') }}</h2>
        <p class="mt-3 text-sm leading-6 text-slate-400">{{ __($description ?? 'Complete the registration in steps. The account is created in users and the profile is stored in members.') }}</p>

        <div class="mt-6 grid gap-3 md:grid-cols-4">
            @foreach ($steps as $index => $step)
                <button
                    type="button"
                    class="rounded-2xl border px-4 py-3 text-left transition"
                    :class="step === {{ $index + 1 }} ? 'border-amber-300/40 bg-amber-400/10 text-amber-100' : 'border-white/10 bg-white/5 text-slate-300 hover:bg-white/10'"
                    @click="goTo({{ $index + 1 }})"
                >
                    <div class="text-xs font-semibold uppercase tracking-[0.25em]">{{ __('Step') }} {{ $index + 1 }}</div>
                    <div class="mt-1 text-sm font-semibold">{{ $step['title'] }}</div>
                </button>
            @endforeach
        </div>
    </div>

    @foreach ($steps as $index => $step)
        <section
            class="rounded-[1.75rem] border border-white/10 bg-slate-950/70 p-6 shadow-xl shadow-slate-950/20 backdrop-blur"
            x-show="step === {{ $index + 1 }}"
            x-transition.opacity.duration.200ms
            data-step="{{ $index + 1 }}"
        >
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/80">{{ __('Step') }} {{ $index + 1 }} {{ __('of') }} {{ count($steps) }}</p>
                    <h3 class="mt-2 font-[family-name:Space_Grotesk] text-2xl font-bold text-white">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-400">{{ $step['description'] }}</p>
                </div>
                <p class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                    {{ $step['title'] }}
                </p>
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach ($step['fields'] as $field)
                    @php
                        $fieldType = $field['type'] ?? 'text';
                        $defaultValue = $field['default'] ?? null;
                        $rawValue = old($field['name'], $defaultValue);
                        $spanClass = ($field['span'] ?? 1) === 2 ? 'md:col-span-2' : '';
                        $fieldReadonly = (bool) ($field['readonly'] ?? false);
                    @endphp

                    <div class="{{ $spanClass }}">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="{{ $field['name'] }}">{{ __($field['label']) }}</label>

                        @if ($fieldType === 'textarea')
                            <textarea
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                rows="4"
                                class="ccims-input"
                                @if (! empty($field['required'])) required @endif
                                @if ($fieldReadonly) readonly @endif
                            >{{ $rawValue }}</textarea>
                        @elseif ($fieldType === 'select')
                            <select
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                class="ccims-input"
                                @if (! empty($field['required'])) required @endif
                                @if ($fieldReadonly) readonly @endif
                            >
                                @foreach ($field['options'] as $value => $label)
                                    <option value="{{ $value }}" @selected((string) $rawValue === (string) $value)>{{ __($label) }}</option>
                                @endforeach
                            </select>
                        @else
                            <input
                                id="{{ $field['name'] }}"
                                type="{{ $fieldType }}"
                                name="{{ $field['name'] }}"
                                value="{{ $fieldType === 'password' ? '' : $rawValue }}"
                                class="ccims-input {{ $fieldReadonly ? 'bg-white/10' : '' }}"
                                @if (! empty($field['required'])) required @endif
                                @if (isset($field['min'])) min="{{ $field['min'] }}" @endif
                                @if (isset($field['max'])) max="{{ $field['max'] }}" @endif
                                @if (isset($field['step'])) step="{{ $field['step'] }}" @endif
                                @if (isset($field['autocomplete'])) autocomplete="{{ $field['autocomplete'] }}" @endif
                                @if ($fieldReadonly) readonly @endif
                            >
                        @endif

                        @if (! empty($field['help']))
                        <p class="mt-2 text-xs text-slate-400">{{ __($field['help']) }}</p>
                        @endif
                        @error($field['name'])
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
                @if ($index > 0)
                    <button
                        type="button"
                        class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5"
                        @click="prev()"
                    >
                        {{ __('Previous') }}
                    </button>
                @else
                    <span></span>
                @endif

                @if ($index < count($steps) - 1)
                    <button
                        type="button"
                        class="rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300"
                        @click="next()"
                    >
                        {{ __('Next') }}
                    </button>
                @else
                    <button type="submit" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                        {{ __($submitLabel ?? 'Create member account') }}
                    </button>
                @endif
            </div>
        </section>
    @endforeach

    @if (! empty($backUrl) || ! empty($backLabel))
        <div class="flex items-center justify-between gap-4">
            @if (! empty($backUrl))
                <a class="rounded-md text-sm text-amber-200 underline decoration-amber-300/40 underline-offset-4 focus:outline-none focus:ring-2 focus:ring-amber-300 focus:ring-offset-2 focus:ring-offset-slate-950" href="{{ $backUrl }}">
                    {{ __($backLabel ?? 'Back') }}
                </a>
            @else
                <span></span>
            @endif
        </div>
    @endif
</form>

<script>
    window.memberRegistrationWizard = function memberRegistrationWizard(initialStep, totalSteps) {
        return {
            step: initialStep,
            totalSteps,
            init() {
                const today = new Date().toISOString().slice(0, 10);
                document.querySelectorAll('input[type="date"]').forEach((input) => {
                    input.max = input.id === 'date_of_birth' ? today : (input.max || today);
                });
            },
            validateStep() {
                const current = document.querySelector(`[data-step="${this.step}"]`);

                if (! current) {
                    return true;
                }

                const fields = current.querySelectorAll('input, textarea, select');

                for (const field of fields) {
                    if (field.readOnly || field.disabled) {
                        continue;
                    }

                    if (typeof field.reportValidity === 'function' && ! field.reportValidity()) {
                        return false;
                    }
                }

                return true;
            },
            next() {
                if (! this.validateStep()) {
                    return;
                }

                if (this.step < this.totalSteps) {
                    this.step += 1;
                }
            },
            prev() {
                if (this.step > 1) {
                    this.step -= 1;
                }
            },
            goTo(step) {
                if (step < this.step) {
                    this.step = step;
                    return;
                }

                while (this.step < step) {
                    if (! this.validateStep()) {
                        return;
                    }

                    this.step += 1;
                }
            },
        };
    };
</script>
