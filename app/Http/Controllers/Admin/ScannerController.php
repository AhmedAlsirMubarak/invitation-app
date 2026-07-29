<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;

class ScannerController extends Controller
{
    public function index(Event $event)
    {
        // Any authenticated staff/admin can open this on their phone at
        // the venue door — no separate app needed, just the camera.
        $stats = $event->attendanceStats();
        $checkinUrl = route('api.checkin');

        return view('scanner.index', compact('event', 'stats', 'checkinUrl'));
    }
}
