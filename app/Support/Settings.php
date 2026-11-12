<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Setting;

class Settings
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::query()->where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'bool', 'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOL),
            'int', 'integer' => $setting->value === null ? $default : (int) $setting->value,
            'json', 'array' => $setting->value ? json_decode($setting->value, true) : $default,
            default => $setting->value ?? $default,
        };
    }

    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): Setting
    {
        $stored = match (true) {
            is_bool($value) => $value ? '1' : '0',
            is_array($value) => json_encode($value, JSON_UNESCAPED_UNICODE),
            is_null($value) => null,
            default => (string) $value,
        };

        $resolvedType = is_bool($value) ? 'bool' : (is_array($value) ? 'json' : $type);

        return Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'type' => $resolvedType, 'group' => $group],
        );
    }
}
