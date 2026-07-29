<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'phone',
        'email',
        'companions_count',
        'table_number',
        'rsvp_status',
        'rsvp_at',
        'token',
        'qr_code_path',
        'invite_sent_at',
        'reminded_at',
    ];

    protected $casts = [
        'rsvp_at' => 'datetime',
        'invite_sent_at' => 'datetime',
        'reminded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Guest $guest) {
            if (empty($guest->token)) {
                // 40-char random token, effectively unguessable — this is
                // what's embedded in the QR code and the public link
                $guest->token = Str::random(40);
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function checkin(): HasOne
    {
        return $this->hasOne(Checkin::class);
    }

    public function invitationUrl(): string
    {
        return url('/i/'.$this->token);
    }
}
