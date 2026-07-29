<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Services\CheckinService;
use Illuminate\Http\Request;

class ScannerAccessController extends Controller
{
    public function __construct(private CheckinService $checkinService)
    {
    }

    public function show(string $token)
    {
        $event = Event::where('scanner_token', $token)->firstOrFail();
        $stats = $event->attendanceStats();
        $checkinUrl = route('scan.checkin', $token);

        return view('scanner.index', compact('event', 'stats', 'checkinUrl'));
    }

    public function checkin(Request $request, string $token)
    {
        $event = Event::where('scanner_token', $token)->firstOrFail();

        $data = $request->validate([
            'token' => ['required', 'string'],
            'action' => ['required', 'in:check_in,check_out'],
        ]);

        $guest = Guest::where('token', $data['token'])->first();

        if (! $guest) {
            return response()->json(['status' => 'error', 'message' => __('admin.checkin.not_recognized')], 404);
        }

        abort_unless($guest->event_id === $event->id, 404);

        $result = $this->checkinService->process($guest, $data['action'], null);
        $status = $result['http_status'];
        unset($result['http_status']);

        return response()->json($result, $status);
    }
}
