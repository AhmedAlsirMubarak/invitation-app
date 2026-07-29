<?php

namespace App\Support;

class EventTypes
{
    // Single source of truth for every event/template type across
    // validation rules, admin+client forms, and translation keys — add a
    // new type here once instead of hunting down every duplicate list.
    public const ALL = [
        'wedding',
        'birthday',
        'party',
        'corporate',
        'graduation',
        'workshop',
        'sports',
        'other',
    ];

    // Types where a physical seating chart makes sense (table number is
    // shown). Left out of ALL's ordering concerns — this is a separate,
    // narrower classification used for conditional UI only.
    public const SEATED = [
        'wedding',
        'birthday',
        'party',
        'corporate',
        'graduation',
    ];

    public static function validationRule(): string
    {
        return 'in:'.implode(',', self::ALL);
    }

    public static function isSeated(string $type): bool
    {
        return in_array($type, self::SEATED, true);
    }
}
