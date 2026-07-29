<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualFields;
use Illuminate\Database\Eloquent\Model;

class LandingFeature extends Model
{
    use HasBilingualFields;

    protected $fillable = ['group', 'icon', 'title', 'body', 'sort_order', 'is_active'];

    protected $casts = [
        'title' => 'array',
        'body' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
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
