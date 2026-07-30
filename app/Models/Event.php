<?php

namespace App\Models;

use App\Support\EventTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'template_id',
        'title',
        'title_en',
        'event_type',
        'event_date',
        'venue_name',
        'venue_map_url',
        'description',
        'invite_message_template',
        'reminder_message_template',
        'cover_image_path',
        'design_overrides',
        'status',
        'package',
        'client_token',
        'scanner_token',
        'guest_quota',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'design_overrides' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->client_token)) {
                // the client uses this to reach their self-service guest
                // portal without an admin login — unguessable, scoped to
                // this one event only
                $event->client_token = Str::random(40);
            }
            if (empty($event->scanner_token)) {
                // separate from client_token so sharing it with door staff
                // doesn't also grant guest-list management access
                $event->scanner_token = Str::random(40);
            }
        });
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    // Basic-package events are invitation-only: no RSVP buttons for guests
    // and no WhatsApp invite/reminder sending for the admin.
    public function hasFullService(): bool
    {
        return $this->package === 'premium';
    }

    public function clientUrl(): string
    {
        return url('/client/'.$this->client_token);
    }

    public function scannerUrl(): string
    {
        return url('/scan/'.$this->scanner_token);
    }

    // English spelling shown on the guest-facing invitation page when
    // viewed in English, falling back to the primary title if none was
    // given. The admin panel always shows the primary title.
    public function displayTitle(): string
    {
        if (app()->getLocale() === 'en' && filled($this->title_en)) {
            return $this->title_en;
        }

        return $this->title;
    }

    // "Guest" reads oddly for a workshop ("attendee") or sports event
    // ("participant") — this is the single place that picks the right noun.
    public function guestNoun(bool $plural = true): string
    {
        $map = trans($plural ? 'admin.guest_noun_plural' : 'admin.guest_noun');

        return $map[$this->event_type] ?? $map['other'];
    }

    // Table numbers only make sense for seated social events, not a
    // workshop or sports event.
    public function showsTableNumber(): bool
    {
        return EventTypes::isSeated($this->event_type);
    }

    // "Companions"/plus-ones is a wedding/social-event concept — doesn't
    // apply to an individual workshop signup.
    public function showsCompanions(): bool
    {
        return $this->event_type !== 'workshop';
    }

    public function companionsLabel(): string
    {
        return $this->event_type === 'sports'
            ? __('admin.guests.field_companions_team')
            : __('admin.guests.field_companions');
    }

    // null = no cap set by the admin, client can add as many as they like
    public function remainingQuota(): ?int
    {
        if ($this->guest_quota === null) {
            return null;
        }

        return max(0, $this->guest_quota - $this->guests()->count());
    }

    public function hasReachedQuota(): bool
    {
        return $this->guest_quota !== null && $this->guests()->count() >= $this->guest_quota;
    }

    // Merges template defaults with per-event overrides so views only
    // need to read one array for colors/fonts.
    public function design(): array
    {
        $defaults = $this->template?->default_config ?? [];
        return array_merge($defaults, $this->design_overrides ?? []);
    }

    public function attendanceStats(): array
    {
        return [
            'invited' => $this->guests()->count(),
            'accepted' => $this->guests()->where('rsvp_status', 'accepted')->count(),
            'declined' => $this->guests()->where('rsvp_status', 'declined')->count(),
            'checked_in' => $this->guests()->whereHas('checkin', fn ($q) => $q->whereNotNull('checked_in_at'))->count(),
            'checked_out' => $this->guests()->whereHas('checkin', fn ($q) => $q->whereNotNull('checked_out_at'))->count(),
            // checked in but hasn't checked out yet — how many people are on-site right now
            'in_venue' => $this->guests()->whereHas('checkin', fn ($q) => $q->whereNotNull('checked_in_at')->whereNull('checked_out_at'))->count(),
        ];
    }
}
