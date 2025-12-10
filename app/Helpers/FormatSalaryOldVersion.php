<?php

namespace App\Helpers;

class FormatSalaryOldVersion
{
    public static function salaryRange($range)
    {
        if (!$range || !is_string($range)) {
            return '-';
        }

        $range = trim($range);

        $parts = preg_split('/\s*-\s*/', $range);

        if (count($parts) === 1) {
            $value = (int) $parts[0];
            return 'Rp ' . number_format($value, 0, ',', '.');
        }

        if (count($parts) >= 2) {
            $min = is_numeric($parts[0]) ? (int) $parts[0] : 0;
            $max = is_numeric($parts[1]) ? (int) $parts[1] : 0;

            if ($min === 0 || $max === 0) {
                return '-';
            }

            return 'Rp ' . number_format($min, 0, ',', '.')
                   . ' - '
                   . number_format($max, 0, ',', '.');
        }

        return '-';
    }
}
