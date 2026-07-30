<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use App\Services\QrCodeService;
use App\Services\WhatsAppLinkService;
use App\Support\GuestImportFile;
use App\Support\PhoneNumber;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function __construct(
        private QrCodeService $qrCodeService,
        private WhatsAppLinkService $whatsApp,
    ) {
    }

    public function index(Event $event, Request $request)
    {
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

        return view('admin.guests.index', compact('event', 'guests', 'counts', 'status'));
    }

    public function store(Request $request, Event $event)
    {
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

        return back()->with('status', __('admin.flash.guest_added', ['name' => $guest->name]));
    }

    // Bulk import: expects a CSV or Excel (.xlsx) file with headers
    // name,name_en,phone,email,companions_count,table_number
    public function import(Request $request, Event $event)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
        ]);

        $file = $request->file('file');
        $rows = GuestImportFile::rows($file->getRealPath(), $file->getClientOriginalExtension());

        $created = 0;
        foreach ($rows as $row) {
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

        return back()->with('status', __('admin.flash.guests_imported', ['count' => $created]));
    }

    public function edit(Event $event, Guest $guest)
    {
        $phone = PhoneNumber::split($guest->phone);

        return view('admin.guests.edit', compact('event', 'guest', 'phone'));
    }

    public function update(Request $request, Event $event, Guest $guest)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'country_code' => ['nullable', 'string', 'max:5'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'companions_count' => ['nullable', 'integer', 'min:0', 'max:20'],
            'table_number' => ['nullable', 'string', 'max:50'],
        ]);
        $data['phone'] = PhoneNumber::combine($data['country_code'] ?? null, $data['phone'] ?? null);
        $data['companions_count'] = $data['companions_count'] ?? 0;
        unset($data['country_code']);

        $guest->update($data);

        return redirect()->route('admin.events.guests.index', $event)->with('status', __('admin.flash.guest_updated', ['name' => $guest->name]));
    }

    public function destroy(Event $event, Guest $guest)
    {
        $guest->delete();

        return back()->with('status', __('admin.flash.guest_removed'));
    }

    // A spotlight-style screen for stepping through every guest with a phone
    // number and sending their WhatsApp invite one click at a time — WhatsApp's
    // click-to-chat links only ever open one chat, so this doesn't remove the
    // manual click, it just removes the round trip back to the guest table
    // between each one.
    public function bulkInvite(Event $event)
    {
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
                'url' => route('admin.events.guests.invite', [$event, $guest]),
                'sent' => (bool) $guest->invite_sent_at,
                'sent_ago' => $guest->invite_sent_at?->diffForHumans(),
            ]);

        $withoutPhoneCount = $event->guests()->where(function ($q) {
            $q->whereNull('phone')->orWhere('phone', '');
        })->count();

        return view('admin.guests.bulk-invite', [
            'event' => $event,
            'guests' => $guests,
            'withoutPhoneCount' => $withoutPhoneCount,
        ]);
    }

    // Opens WhatsApp pre-filled with the invitation text and marks it sent.
    public function sendInvite(Event $event, Guest $guest)
    {
        abort_unless($event->hasFullService(), 403);

        $link = $this->whatsApp->inviteLink($guest);
        if (! $link) {
            return back()->with('status', __('admin.flash.no_phone_on_file', ['name' => $guest->name]));
        }

        $guest->update(['invite_sent_at' => now()]);

        return redirect()->away($link);
    }

    // Opens WhatsApp pre-filled with a day-before reminder and marks it sent.
    public function sendReminder(Event $event, Guest $guest)
    {
        abort_unless($event->hasFullService(), 403);

        $link = $this->whatsApp->reminderLink($guest);
        if (! $link) {
            return back()->with('status', __('admin.flash.no_phone_on_file', ['name' => $guest->name]));
        }

        $guest->update(['reminded_at' => now()]);

        return redirect()->away($link);
    }
}
