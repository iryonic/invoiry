<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BrochureSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting by key with fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("brochure_setting_{$key}", 3600, function () use ($key, $default) {
            try {
                $setting = static::where('key', $key)->first();

                return $setting ? $setting->value : $default;
            } catch (\Throwable) {
                return $default;
            }
        });
    }

    /**
     * Store or update a brochure setting.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value, 'group' => $group]
        );

        Cache::forget("brochure_setting_{$key}");
    }

    /**
     * Check if a setting is truthy.
     */
    public static function isEnabled(string $key, bool $default = true): bool
    {
        $val = static::get($key, $default ? '1' : '0');

        return in_array($val, ['1', 'true', true, 1], true);
    }
}
