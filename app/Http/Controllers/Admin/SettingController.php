<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends CrudController
{
    protected function settingDefinitions(): array
    {
        return [
            'auto_approve_payments' => [
                'label' => 'Auto approve payments',
                'description' => 'Approve eligible payments automatically without manual review.',
                'help' => 'Turn this on to reduce manual payment approval work.',
                'type' => 'toggle',
                'value_type' => 'bool',
            ],
            'email_enabled' => [
                'label' => 'Email notifications',
                'description' => 'Control whether the system sends email notifications at all.',
                'help' => 'Use this as the master email delivery switch.',
                'type' => 'toggle',
                'value_type' => 'bool',
            ],
            'checkout_eligible_months' => [
                'label' => 'Checkout eligibility window',
                'description' => 'Set how many months must pass after joining before a member can checkout.',
                'help' => 'Enter the number of months only.',
                'type' => 'number',
                'value_type' => 'int',
                'suffix' => 'months',
                'min' => 1,
                'max' => 120,
                'step' => 1,
            ],
            'notification_channels' => [
                'label' => 'Notification channels',
                'description' => 'Enable or disable the notification delivery channels used by the system.',
                'help' => 'Switch this on to keep notifications active.',
                'type' => 'toggle',
                'value_type' => 'bool',
            ],
        ];
    }

    protected function definitionFor(?Model $record = null): array
    {
        $key = $record?->key ?? request()->route('setting')?->key;

        return $this->settingDefinitions()[$key] ?? [
            'label' => $key ? Str::headline($key) : 'Setting',
            'description' => 'Legacy setting value.',
            'help' => 'This setting is stored using the legacy generic editor.',
            'type' => 'text',
            'value_type' => $record?->value_type ?? 'string',
        ];
    }

    protected function modelClass(): string
    {
        return Setting::class;
    }

    protected function title(): string
    {
        return 'Settings';
    }

    protected function viewPrefix(): string
    {
        return 'settings';
    }

    protected function routeParameter(): string
    {
        return 'setting';
    }

    protected function pageDescription(): string
    {
        return 'Each operational setting has its own card and control.';
    }

    public function index(Request $request): View
    {
        $this->requirePermission($request, 'view');

        $definitions = $this->settingDefinitions();
        $records = Setting::query()
            ->whereIn('key', array_keys($definitions))
            ->get()
            ->keyBy('key');

        return view('admin.settings.index', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'definitions' => $definitions,
            'records' => $records,
            'canUpdate' => $this->can($request, 'update'),
        ]);
    }

    public function create(): View
    {
        abort(404);
    }

    public function store(Request $request): RedirectResponse
    {
        abort(404);
    }

    public function show(Request $request): View
    {
        abort(404);
    }

    public function edit(Request $request): View
    {
        abort(404);
    }

    protected function rules(?Model $record = null): array
    {
        $definition = $this->definitionFor($record);
        $numberRules = ['required', 'integer'];

        if (isset($definition['min'])) {
            $numberRules[] = 'min:'.$definition['min'];
        }

        if (isset($definition['max'])) {
            $numberRules[] = 'max:'.$definition['max'];
        }

        if ($definition['type'] === 'toggle') {
            return [
                'value' => ['required', 'boolean'],
            ];
        }

        if ($definition['type'] === 'number') {
            return [
                'value' => $numberRules,
            ];
        }

        return [
            'value' => ['required', 'string'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        $definition = $this->definitionFor($record);

        $input['value_type'] = $definition['value_type'];

        if ($definition['type'] === 'toggle') {
            $input['value'] = filter_var($input['value'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
        } elseif ($definition['type'] === 'number') {
            $input['value'] = (string) (int) $input['value'];
        }

        return $input;
    }

    public function update(Request $request): RedirectResponse
    {
        $this->requirePermission($request, 'update');

        $record = $this->resolveRecord($request);
        $data = $request->validate(array_merge([
            'setting_key' => ['required', 'string', Rule::in([$record->key])],
        ], $this->rules($record)));
        $data = $this->transformInput($data, $record);
        $data['updated_by'] = $request->user()?->id;

        $record->update($data);
        $this->afterSave($record, $data, $request);

        return redirect()->route("admin.{$this->viewPrefix()}.index")->with('status', 'updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        abort(404);
    }

    protected function afterSave(Model $record, array $data, Request $request): void
    {
        Cache::forget("ccims.setting.{$record->key}");
    }
}
