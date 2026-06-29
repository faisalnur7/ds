<?php

namespace App\Http\Controllers\Admin;

use App\Models\ShareSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShareSettingController extends CrudController
{
    protected function modelClass(): string
    {
        return ShareSetting::class;
    }

    protected function title(): string
    {
        return 'Share Settings';
    }

    protected function viewPrefix(): string
    {
        return 'share-settings';
    }

    protected function routeParameter(): string
    {
        return 'share_setting';
    }

    protected function pageDescription(): string
    {
        return 'Versioned share values and cost rules. Only one setting should be active at a time.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Effective From', 'key' => 'effective_from', 'type' => 'date'],
            ['label' => 'Share Value', 'key' => 'share_value', 'type' => 'money'],
            ['label' => 'Share Cost', 'key' => 'share_cost', 'type' => 'money'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'bool'],
        ];
    }

    protected function query(): Builder
    {
        return ShareSetting::query()
            ->orderByDesc('effective_from')
            ->orderByDesc('id');
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'share_value', 'label' => 'Share Value', 'type' => 'number'],
            ['name' => 'share_cost', 'label' => 'Share Cost', 'type' => 'number'],
            ['name' => 'effective_from', 'label' => 'Effective From', 'type' => 'date'],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'toggle', 'helper' => 'Activating this version disables the other active version.'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'share_value' => ['required', 'numeric', 'min:0'],
            'share_cost' => ['required', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        $input['is_active'] = (bool) ($input['is_active'] ?? false);
        return $input;
    }

    protected function afterSave(Model $record, array $data, Request $request): void
    {
        if ($record->is_active) {
            $this->activateRecord($record);
        }

        if (! ShareSetting::active()->exists()) {
            $fallback = ShareSetting::query()
                ->orderByDesc('effective_from')
                ->orderByDesc('id')
                ->first();

            if ($fallback) {
                $fallback->forceFill(['is_active' => true])->saveQuietly();
            }
        }
    }

    public function toggle(ShareSetting $share_setting): RedirectResponse
    {
        $this->toggleRecord($share_setting);

        return redirect()
            ->route("admin.{$this->viewPrefix()}.index")
            ->with('status', 'updated');
    }

    public function activate(ShareSetting $share_setting): RedirectResponse
    {
        return $this->toggle($share_setting);
    }

    protected function activateRecord(ShareSetting $record): void
    {
        DB::transaction(function () use ($record): void {
            ShareSetting::query()
                ->whereKeyNot($record->getKey())
                ->update(['is_active' => false]);

            $record->forceFill(['is_active' => true])->saveQuietly();
        });
    }

    protected function toggleRecord(ShareSetting $record): void
    {
        DB::transaction(function () use ($record): void {
            if ($record->is_active) {
                $record->forceFill(['is_active' => false])->saveQuietly();

                return;
            }

            ShareSetting::query()
                ->whereKeyNot($record->getKey())
                ->update(['is_active' => false]);

            $record->forceFill(['is_active' => true])->saveQuietly();
        });
    }
}
