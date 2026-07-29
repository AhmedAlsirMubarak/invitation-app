<?php

namespace App\Services;

use App\Models\Checkin;
use App\Models\Guest;

class CheckinService
{
    // Shared by the admin-authenticated scanner (Api\CheckinController) and
    // the public per-event scanner link (ScannerAccessController) so the
    // duplicate-detection/check-out rules can't drift between the two.
    public function process(Guest $guest, string $action, ?int $scannedBy): array
    {
        $checkin = Checkin::firstOrNew(['guest_id' => $guest->id]);

        if ($action === 'check_in') {
            if ($checkin->exists && $checkin->checked_in_at) {
                return [
                    'http_status' => 200,
                    'status' => 'duplicate',
                    'message' => __('admin.checkin.already_checked_in', ['name' => $guest->name, 'time' => $checkin->checked_in_at->format('g:i A')]),
                    'guest' => $this->guestPayload($guest),
                ];
            }
            $checkin->checked_in_at = now();
        } else {
            if (! $checkin->exists || ! $checkin->checked_in_at) {
                return [
                    'http_status' => 422,
                    'status' => 'error',
                    'message' => __('admin.checkin.not_checked_in_yet', ['name' => $guest->name]),
                ];
            }
            if ($checkin->checked_out_at) {
                return [
                    'http_status' => 200,
                    'status' => 'duplicate',
                    'message' => __('admin.checkin.already_checked_out', ['name' => $guest->name, 'time' => $checkin->checked_out_at->format('g:i A')]),
                ];
            }
            $checkin->checked_out_at = now();
        }

        $checkin->guest_id = $guest->id;
        $checkin->scanned_by = $scannedBy;
        $checkin->save();

        return [
            'http_status' => 200,
            'status' => 'ok',
            'message' => $action === 'check_in'
                ? __('admin.checkin.checked_in', ['name' => $guest->name])
                : __('admin.checkin.checked_out', ['name' => $guest->name]),
            'guest' => $this->guestPayload($guest),
            'stats' => $guest->event->attendanceStats(),
        ];
    }

    // table_number/companions_count are seated-social-event concepts —
    // omit them for event types where they're not applicable so the
    // scanner UI doesn't show empty/irrelevant fields.
    private function guestPayload(Guest $guest): array
    {
        $event = $guest->event;

        return array_filter([
            'name' => $guest->name,
            'table_number' => $event->showsTableNumber() ? $guest->table_number : null,
            'companions_count' => $event->showsCompanions() ? $guest->companions_count : null,
        ], fn ($value, $key) => $key === 'name' || $value !== null, ARRAY_FILTER_USE_BOTH);
    }
}
