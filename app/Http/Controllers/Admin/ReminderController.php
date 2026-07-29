<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Support\Carbon;

class ReminderController extends Controller
{
    // Guests whose event is tomorrow and who confirmed attendance — the
    // people worth nudging the day before, per munasbatsa's "reminder to
    // confirmed guests" behavior.
    public function index()
    {
        $tomorrow = Carbon::tomorrow();

        $guests = Guest::query()
            ->with('event')
            ->where('rsvp_status', 'accepted')
            ->whereHas('event', fn ($q) => $q->whereDate('event_date', $tomorrow)->where('package', 'premium'))
            ->orderBy('name')
            ->get();

        $pending = $guests->whereNull('reminded_at');
        $alreadyReminded = $guests->whereNotNull('reminded_at');

        return view('admin.reminders.index', compact('pending', 'alreadyReminded'));
    }
}
