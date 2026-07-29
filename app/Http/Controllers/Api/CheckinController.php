<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Services\CheckinService;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function __construct(private CheckinService $checkinService)
    {
    }

    // Called by the scanner page's JS after it reads a QR code. Token is
    // the only thing encoded in the QR — everything else is looked up
    // here so nothing sensitive travels in the code itself.
    public function store(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'action' => ['required', 'in:check_in,check_out'],
        ]);

        $guest = Guest::where('token', $data['token'])->first();

        if (! $guest) {
            return response()->json(['status' => 'error', 'message' => __('admin.checkin.not_recognized')], 404);
        }

        $result = $this->checkinService->process($guest, $data['action'], $request->user()?->id);
        $status = $result['http_status'];
        unset($result['http_status']);

        return response()->json($result, $status);
    }
}
