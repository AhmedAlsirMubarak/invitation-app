<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'thumbnail_path',
        'background_image_path',
        'blade_view',
        'default_config',
        'is_active',
    ];

    protected $casts = [
        'default_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function backgroundImageUrl(): ?string
    {
        return $this->background_image_path
            ? Storage::disk('public')->url($this->background_image_path)
            : null;
    }
}
