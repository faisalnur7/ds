<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("ccims.setting.{$key}", function () use ($key, $default) {
            $setting = Setting::query()->where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return match ($setting->value_type) {
                'bool' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'int' => (int) $setting->value,
                'float' => (float) $setting->value,
                'json' => json_decode($setting->value, true, 512, JSON_THROW_ON_ERROR),
                default => $setting->value,
            };
        });
    }

    public function put(string $key, mixed $value, string $type = 'string', ?int $updatedBy = null): Setting
    {
        $storedValue = match ($type) {
            'bool' => $value ? '1' : '0',
            'int', 'float', 'string' => (string) $value,
            'json' => json_encode($value, JSON_THROW_ON_ERROR),
            default => (string) $value,
        };

        $setting = Setting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'value_type' => $type,
                'updated_by' => $updatedBy,
            ]
        );

        Cache::forget("ccims.setting.{$key}");

        return $setting;
    }
}
