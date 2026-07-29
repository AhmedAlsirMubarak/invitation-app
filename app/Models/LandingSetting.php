<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $fillable = ['key', 'type', 'value', 'image_path'];

    protected $casts = [
        'value' => 'array',
    ];

    // Text/textarea value in the current app locale.
    public static function text(string $key, string $default = ''): string
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        $value = $setting->value ?? [];

        return $value[app()->getLocale()] ?? $value['ar'] ?? $default;
    }

    public static function imageUrl(string $key): ?string
    {
        $setting = static::where('key', $key)->first();

        return $setting?->image_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($setting->image_path)
            : null;
    }

    // Site logo, admin-uploadable from Landing Settings. Falls back to the
    // bundled static file so the brand mark still renders before an admin
    // ever uploads one. $key = 'logo' (for light backgrounds) or
    // 'logo_dark' (white version, for dark backgrounds).
    public static function logoUrl(string $key = 'logo'): string
    {
        $fallback = $key === 'logo_dark' ? 'qrdukhool-white.png' : 'qrdukhool-logo.png';

        return static::imageUrl($key) ?? asset('images/'.$fallback);
    }
}
