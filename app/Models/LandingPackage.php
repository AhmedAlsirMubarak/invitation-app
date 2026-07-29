<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualFields;
use Illuminate\Database\Eloquent\Model;

class LandingPackage extends Model
{
    use HasBilingualFields;

    protected $fillable = [
        'name', 'price_label', 'description', 'features', 'badge', 'cta_label', 'cta_url', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'price_label' => 'array',
        'description' => 'array',
        'features' => 'array',
        'badge' => 'array',
        'cta_label' => 'array',
        'is_active' => 'boolean',
    ];

    // Feature bullet list in the current app locale.
    public function featureLines(): array
    {
        $locale = app()->getLocale();

        return collect($this->features ?? [])
            ->map(fn ($f) => $f[$locale] ?? $f['ar'] ?? $f['en'] ?? '')
            ->filter()
            ->values()
            ->all();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
