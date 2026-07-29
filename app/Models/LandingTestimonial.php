<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LandingTestimonial extends Model
{
    use HasBilingualFields;

    protected $fillable = ['author_name', 'event_category', 'quote', 'rating', 'avatar_path', 'sort_order', 'is_active'];

    protected $casts = [
        'event_category' => 'array',
        'quote' => 'array',
        'is_active' => 'boolean',
    ];

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null;
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
