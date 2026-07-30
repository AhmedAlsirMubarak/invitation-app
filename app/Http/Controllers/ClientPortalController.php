<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Services\QrCodeService;
use App\Services\WhatsAppLinkService;
use App\Support\EventTypes;
use App\Support\GuestImportFile;
use App\Support\PhoneNumber;
use Illuminate\Http\Request;

class ClientPortalController extends Controller
{
    public function __construct(
        private QrCodeService $qrCodeService,
        private WhatsAppLinkService $whatsApp,
    ) {
    }

    private function findEvent(string $token): Event
    {
        return Event::where('client_token', $token)->firstOrFail();
    }

    public function show(string $token, Request $request)
    {
        $event = $this->findEvent($token);

        $counts = [
            'all' => $event->guests()->count(),
            'pending' => $event->guests()->where('rsvp_status', 'pending')->count(),
            'accepted' => $event->guests()->where('rsvp_status', 'accepted')->count(),
            'declined' => $event->guests()->where('rsvp_status', 'declined')->count(),
        ];

        $status = $request->query('status', 'all');
        $guests = $event->guests()->with('checkin')
            ->when($status !== 'all', fn ($q) => $q->where('rsvp_status', $status))
            ->orderBy('name')->get();

        return view('client.show', compact('event', 'guests', 'counts', 'status'));
    }

    public function editEvent(string $token)
    {
        $event = $this->findEvent($token);

        return view('client.edit-event', compact('event'));
    }

    // Deliberately scoped to informational fields only — status, package,
    // guest_quota, template and design colors stay admin-only.
    public function updateEvent(Request $request, string $token)
    {
        $event = $this->findEvent($token);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'event_type' => ['required', 'string', EventTypes::validationRule()],
            'event_date' => ['required', 'date'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_map_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string'],
        ]);

        $event->update($data);

        return redirect()->route('client.show', $token)->with('status', __('client.event_updated'));
    }

    public function storeGuest(Request $request, string $token)
    {
        $event = $this->findEvent($token);

        if ($event->hasReachedQuota()) {
            return back()->with('status', __('client.quota_reached'));
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'country_code' => ['nullable', 'string', 'max:5'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'companions_count' => ['nullable', 'integer', 'min:0', 'max:20'],
            'table_number' => ['nullable', 'string', 'max:50'],
        ]);
        $data['event_id'] = $event->id;
        $data['phone'] = PhoneNumber::combine($data['country_code'] ?? null, $data['phone'] ?? null);
        $data['companions_count'] = $data['companions_count'] ?? 0;
        unset($data['country_code']);

        $guest = Guest::create($data);
        $this->qrCodeService->generateForGuest($guest);

        return back()->with('status', __('client.guest_added', ['name' => $guest->name]));
    }

    public function importGuests(Request $request, string $token)
    {
        $event = $this->findEvent($token);

        if ($event->hasReachedQuota()) {
            return back()->with('status', __('client.quota_reached'));
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
        ]);

        $remaining = $event->remainingQuota();

        $file = $request->file('file');
        $rows = GuestImportFile::rows($file->getRealPath(), $file->getClientOriginalExtension());

        $created = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            if ($remaining !== null && $created >= $remaining) {
                $skipped++;
                continue;
            }

            if (empty($row['name'])) {
                continue;
            }

            $guest = Guest::create([
                'event_id' => $event->id,
                'name' => trim($row['name']),
                'name_en' => ! empty($row['name_en']) ? trim($row['name_en']) : null,
                'phone' => $row['phone'] ?? null,
                'email' => $row['email'] ?? null,
                'companions_count' => (int) ($row['companions_count'] ?? 0),
                'table_number' => $row['table_number'] ?? null,
            ]);
            $this->qrCodeService->generateForGuest($guest);
            $created++;
        }

        $message = __('client.guests_imported', ['count' => $created]);
        if ($skipped > 0) {
            $message .= ' '.__('client.guests_skipped_quota', ['count' => $skipped]);
        }

        return back()->with('status', $message);
    }

    public function destroyGuest(string $token, Guest $guest)
    {
        $event = $this->findEvent($token);
        abort_unless($guest->event_id === $event->id, 404);

        $guest->delete();

        return back()->with('status', __('client.guest_removed'));
    }

    public function bulkInvite(string $token)
    {
        $event = $this->findEvent($token);
        abort_unless($event->hasFullService(), 403);

        $guests = $event->guests()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderByRaw('invite_sent_at IS NOT NULL')
            ->orderBy('name')
            ->get()
            ->map(fn (Guest $guest) => [
                'id' => $guest->id,
                'name' => $guest->name,
                'phone' => $guest->phone,
                'url' => route('client.guests.invite', [$token, $guest]),
                'sent' => (bool) $guest->invite_sent_at,
                'sent_ago' => $guest->invite_sent_at?->diffForHumans(),
            ]);

        $withoutPhoneCount = $event->guests()->where(function ($q) {
            $q->whereNull('phone')->orWhere('phone', '');
        })->count();

        return view('client.bulk-invite', [
            'event' => $event,
            'token' => $token,
            'guests' => $guests,
            'withoutPhoneCount' => $withoutPhoneCount,
        ]);
    }

    public function sendInvite(string $token, Guest $guest)
    {
        $event = $this->findEvent($token);
        abort_unless($guest->event_id === $event->id, 404);
        abort_unless($event->hasFullService(), 403);

        $link = $this->whatsApp->inviteLink($guest);
        if (! $link) {
            return back()->with('status', __('client.no_phone_on_file', ['name' => $guest->name]));
        }

        $guest->update(['invite_sent_at' => now()]);

        return redirect()->away($link);
    }
}
