<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LandingShowcaseItem extends Model
{
    use HasBilingualFields;

    protected $fillable = ['image_path', 'caption', 'sort_order', 'is_active'];

    protected $casts = [
        'caption' => 'array',
        'is_active' => 'boolean',
    ];

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
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
