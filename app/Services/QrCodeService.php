<?php

namespace App\Services;

use App\Models\Guest;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * Generates a PNG QR code that encodes the guest's public invitation
     * token (not personal data), stores it on the public disk, and saves
     * the path on the guest record.
     */
    public function generateForGuest(Guest $guest): string
    {
        // The QR payload is just the token — the scanner looks this up
        // server-side, so nothing sensitive is ever embedded in the code.
        $qrCode = new QrCode($guest->token);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $path = 'qr-codes/'.$guest->event_id.'/'.$guest->token.'.png';
        Storage::disk('public')->put($path, $result->getString());

        $guest->update(['qr_code_path' => $path]);

        return $path;
    }

    public function urlFor(Guest $guest): ?string
    {
        return $guest->qr_code_path
            ? Storage::disk('public')->url($guest->qr_code_path)
            : null;
    }
}
