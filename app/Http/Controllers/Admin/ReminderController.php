<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReminderController extends Controller
{
    // Confirmed guests of this event whose event is tomorrow — the people
    // worth nudging the day before, per munasbatsa's "reminder to confirmed
    // guests" behavior. Any other day, there's nothing to remind yet.
    public function index(Event $event)
    {
        abort_unless($event->hasFullService(), 403);

        $guests = $event->event_date->isSameDay(Carbon::tomorrow())
            ? $event->guests()->where('rsvp_status', 'accepted')->orderBy('name')->get()
            : new Collection;

        $pending = $guests->whereNull('reminded_at');
        $alreadyReminded = $guests->whereNotNull('reminded_at');

        return view('admin.reminders.index', compact('event', 'pending', 'alreadyReminded'));
    }
}
