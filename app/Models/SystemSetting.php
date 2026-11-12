<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Retrieve a setting value by key with caching.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            try {
                $setting = static::where('key', $key)->first();

                return $setting ? $setting->value : $default;
            } catch (\Throwable) {
                return $default;
            }
        });
    }

    /**
     * Set / update a setting value by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'group' => $group]
        );

        Cache::forget("system_setting_{$key}");
    }

    /**
     * Check if boolean setting is enabled.
     */
    public static function isEnabled(string $key, bool $default = false): bool
    {
        $val = static::get($key, $default ? '1' : '0');

        return in_array($val, ['1', 1, 'true', true, 'yes', 'on'], true);
    }
}
