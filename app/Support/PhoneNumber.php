<?php

namespace App\Support;

class PhoneNumber
{
    // Curated for this app's market — Gulf/Arab countries first, plus a
    // few international majors. Not an exhaustive world list.
    public const COUNTRIES = [
        '968' => 'Oman (+968)',
        '966' => 'Saudi Arabia (+966)',
        '971' => 'United Arab Emirates (+971)',
        '965' => 'Kuwait (+965)',
        '974' => 'Qatar (+974)',
        '973' => 'Bahrain (+973)',
        '249' => 'Sudan (+249)',
        '20' => 'Egypt (+20)',
        '962' => 'Jordan (+962)',
        '961' => 'Lebanon (+961)',
        '964' => 'Iraq (+964)',
        '212' => 'Morocco (+212)',
        '216' => 'Tunisia (+216)',
        '213' => 'Algeria (+213)',
        '1' => 'US / Canada (+1)',
        '44' => 'United Kingdom (+44)',
    ];

    // Combines a selected country code with a locally-entered number into
    // one stored value, e.g. ('971', '501234567') -> '+971501234567'.
    public static function combine(?string $countryCode, ?string $local): ?string
    {
        $local = preg_replace('/\D+/', '', (string) $local);
        $local = ltrim($local, '0');
        if ($local === '') {
            return null;
        }

        $code = preg_replace('/\D+/', '', $countryCode ?: '966');

        return '+'.$code.$local;
    }

    // Splits a stored phone value back into (country_code, local) so an
    // edit form can preselect the right country. Falls back to Saudi if
    // no known code prefix matches (e.g. legacy locally-formatted numbers).
    public static function split(?string $phone): array
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return ['country_code' => '966', 'local' => ''];
        }

        $codes = array_keys(self::COUNTRIES);
        usort($codes, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($codes as $code) {
            if (str_starts_with($digits, $code)) {
                return ['country_code' => $code, 'local' => substr($digits, strlen($code))];
            }
        }

        return ['country_code' => '966', 'local' => ltrim($digits, '0')];
    }
}
