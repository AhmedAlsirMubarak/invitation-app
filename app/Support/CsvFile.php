<?php

namespace App\Support;

class CsvFile
{
    // Excel's Arabic-locale CSV export is commonly Windows-1256, and its
    // "Unicode Text" export is UTF-16 — neither is UTF-8. Reading either
    // straight into a UTF-8 column turns Arabic characters into literal
    // "?" — and once that happens the original text is gone for good, not
    // just a display glitch. Detect and transparently convert before
    // handing back a stream ready for fgetcsv().
    public static function openUtf8(string $path)
    {
        $content = file_get_contents($path);

        if (str_starts_with($content, "\xFF\xFE")) {
            // UTF-16 LE with BOM
            $content = self::tryIconv($content, 'UTF-16LE', 2) ?? $content;
        } elseif (str_starts_with($content, "\xFE\xFF")) {
            // UTF-16 BE with BOM
            $content = self::tryIconv($content, 'UTF-16BE', 2) ?? $content;
        } elseif (str_starts_with($content, "\xEF\xBB\xBF")) {
            // UTF-8 with BOM — already fine, just strip the marker
            $content = substr($content, 3);
        } elseif (! mb_check_encoding($content, 'UTF-8')) {
            // mbstring doesn't recognize "Windows-1256" as a conversion
            // target/source on every PHP build; iconv does.
            $content = self::tryIconv($content, 'WINDOWS-1256', 0) ?? $content;
        }

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        return $stream;
    }

    private static function tryIconv(string $content, string $fromEncoding, int $skipBytes): ?string
    {
        $converted = @iconv($fromEncoding, 'UTF-8//TRANSLIT', substr($content, $skipBytes));

        return $converted === false ? null : $converted;
    }
}
