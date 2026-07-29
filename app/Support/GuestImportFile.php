<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\IOFactory;

class GuestImportFile
{
    // Returns each data row as an associative array keyed by lowercased
    // header (name, phone, email, companions_count, table_number),
    // regardless of whether the upload was a CSV/TXT file (any common
    // encoding, via CsvFile) or an .xlsx workbook. Reading .xlsx directly
    // sidesteps CSV character-encoding guessing entirely — Excel stores
    // text as proper Unicode internally.
    public static function rows(string $path, string $extension): array
    {
        return strtolower($extension) === 'xlsx'
            ? self::xlsxRows($path)
            : self::csvRows($path);
    }

    private static function xlsxRows(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $data = $sheet->toArray(null, true, true, false);

        if (empty($data)) {
            return [];
        }

        $header = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($data));

        return array_map(
            fn ($row) => array_combine($header, array_pad(
                array_map(fn ($v) => $v === null ? '' : trim((string) $v), $row),
                count($header),
                ''
            )),
            $data
        );
    }

    private static function csvRows(string $path): array
    {
        $handle = CsvFile::openUtf8($path);
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);

            return [];
        }
        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($header, array_pad($row, count($header), ''));
        }
        fclose($handle);

        return $rows;
    }
}
