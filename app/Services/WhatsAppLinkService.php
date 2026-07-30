<?php

namespace App\Services;

use App\Models\Guest;
use Carbon\Carbon;

class WhatsAppLinkService
{
    // Placeholders available to an admin's custom message template.
    public const PLACEHOLDERS = ['{name}', '{event_title}', '{date}', '{time}', '{venue}', '{venue_map_url}', '{invitation_url}'];

    /**
     * A wa.me link pre-filled with the invitation text, or null if the
     * guest has no phone number on file.
     */
    public function inviteLink(Guest $guest): ?string
    {
        $phone = $this->normalizePhone($guest->phone);
        if (! $phone) {
            return null;
        }

        return $this->buildLink($phone, $this->inviteMessage($guest));
    }

    /**
     * A wa.me link pre-filled with a day-before reminder, or null if the
     * guest has no phone number on file.
     */
    public function reminderLink(Guest $guest): ?string
    {
        $phone = $this->normalizePhone($guest->phone);
        if (! $phone) {
            return null;
        }

        return $this->buildLink($phone, $this->reminderMessage($guest));
    }

    // Admin's own template (with placeholders filled in) when set on the
    // event, otherwise a canned message chosen by event_type.
    public function inviteMessage(Guest $guest): string
    {
        $event = $guest->event;

        if (filled($event->invite_message_template)) {
            return strtr($event->invite_message_template, $this->placeholders($guest));
        }

        return $this->defaultInviteMessage($guest);
    }

    public function reminderMessage(Guest $guest): string
    {
        $event = $guest->event;

        if (filled($event->reminder_message_template)) {
            return strtr($event->reminder_message_template, $this->placeholders($guest));
        }

        return $this->defaultReminderMessage($guest);
    }

    private function placeholders(Guest $guest): array
    {
        $event = $guest->event;
        Carbon::setLocale('ar');

        return [
            '{name}' => $guest->name,
            '{event_title}' => $event->title,
            '{date}' => $event->event_date->translatedFormat('l، d F Y'),
            '{time}' => $event->event_date->translatedFormat('g:i A'),
            '{venue}' => $event->venue_name ?? '',
            '{venue_map_url}' => $event->venue_map_url ?? '',
            '{invitation_url}' => $guest->invitationUrl(),
        ];
    }

    private function defaultInviteMessage(Guest $guest): string
    {
        $event = $guest->event;
        $p = $this->placeholders($guest);

        $lines = match ($event->event_type) {
            'wedding' => [
                "الفاضلة/الفاضل: {$p['{name}']}",
                'السلام عليكم،',
                '',
                "يتشرف بدعوتكم لحضور {$p['{event_title}']}",
            ],
            'birthday', 'party' => [
                "عزيزي/عزيزتي {$p['{name}']}،",
                '',
                "أنت مدعو لحضور {$p['{event_title}']} 🎉",
            ],
            'corporate' => [
                "السيد/ة {$p['{name}']}،",
                '',
                "يسرنا دعوتكم لحضور {$p['{event_title}']}",
            ],
            'graduation' => [
                "عزيزي/عزيزتي {$p['{name}']}،",
                '',
                "يسرنا دعوتكم للاحتفال بمناسبة التخرج — {$p['{event_title}']} 🎓",
            ],
            'workshop' => [
                "مرحبًا {$p['{name}']}،",
                '',
                "تم تسجيلك في ورشة {$p['{event_title}']}",
            ],
            'sports' => [
                "مرحبًا {$p['{name}']}،",
                '',
                "تم تسجيلك في {$p['{event_title}']} 🏆",
            ],
            default => [
                "مرحبًا {$p['{name}']}،",
                '',
                "أنت مدعو لحضور {$p['{event_title}']}",
            ],
        };

        $lines[] = '  '.$p['{date}'];
        $lines[] = '🕕 '.$p['{time}'];

        if ($p['{venue}']) {
            $lines[] = "📍 {$p['{venue}']}";
        }
        if ($p['{venue_map_url}']) {
            $lines[] = $p['{venue_map_url}'];
        }

        $lines[] = '';
        $lines[] = match ($event->event_type) {
            'workshop' => 'يرجى تأكيد الحضور عبر الرابط:',
            'sports' => 'للتفاصيل والتأكيد:',
            default => 'يرجى تأكيد الحضور أو الاعتذار عبر الرابط:',
        };
        $lines[] = $p['{invitation_url}'];

        return implode("\n", $lines);
    }

    private function defaultReminderMessage(Guest $guest): string
    {
        $event = $guest->event;
        $p = $this->placeholders($guest);

        $lines = [
            match ($event->event_type) {
                'workshop' => "تذكير: غدًا موعد ورشة {$p['{event_title}']}",
                'sports' => "تذكير: غدًا موعد {$p['{event_title}']} 🏆",
                default => "تذكير 🎉 غدًا بإذن الله موعد {$p['{event_title}']}",
            },
            ' '.$p['{date}'],
            '🕕 '.$p['{time}'],
        ];

        if ($p['{venue}']) {
            $lines[] = "📍 {$p['{venue}']}";
        }
        if ($p['{venue_map_url}']) {
            $lines[] = $p['{venue_map_url}'];
        }

        $lines[] = '';
        $lines[] = 'بانتظار حضوركم ✨';
        $lines[] = $p['{invitation_url}'];

        return implode("\n", $lines);
    }

    private function buildLink(string $phone, string $message): string
    {
        return 'https://api.whatsapp.com/send?phone='.$phone.'&text='.rawurlencode($message);
    }

    /**
     * Normalizes to WhatsApp's expected "digits only, with country code"
     * format. Assumes Saudi numbers (05xxxxxxxx -> 9665xxxxxxxx) when a
     * local-format number is given, since that's this app's primary market.
     */
    private function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = '966'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '966') && strlen($digits) <= 10) {
            $digits = '966'.$digits;
        }

        return $digits;
    }
}
