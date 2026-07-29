<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestInvitationController extends Controller
{
    public function show(string $token)
    {
        $guest = Guest::where('token', $token)->firstOrFail();
        $guest->load('event.template');

        return view('invitations.show', compact('guest'));
    }

    public function rsvp(Request $request, string $token)
    {
        $guest = Guest::where('token', $token)->firstOrFail();

        abort_unless($guest->event->hasFullService(), 403);

        $data = $request->validate([
            'rsvp_status' => ['required', 'in:accepted,declined'],
            'companions_count' => ['nullable', 'integer', 'min:0', 'max:20'],
        ]);
        $data['rsvp_at'] = now();

        $guest->update($data);

        return back()->with('status', __('invite.rsvp_saved'));
    }
}
