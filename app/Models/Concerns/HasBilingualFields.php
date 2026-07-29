<?php

namespace App\Models\Concerns;

trait HasBilingualFields
{
    // Reads a {"ar": "...", "en": "..."} JSON field in the current app
    // locale, falling back to Arabic then English if the current locale
    // is missing (e.g. content was only filled in for one language).
    public function t(string $field): string
    {
        $value = $this->{$field} ?? [];

        return $value[app()->getLocale()] ?? $value['ar'] ?? $value['en'] ?? '';
    }
}
