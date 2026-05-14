<?php

namespace App\Support;

use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Jalali ↔ Gregorian conversion (same algorithm as bootstrap-datepicker.fa.min.js).
 */
final class JalaliCalendar
{
    /**
     * @return array{0:int,1:int,2:int} Gregorian year, month (1–12), day
     */
    public static function jalaliToGregorian(int $jy, int $jm, int $jd): array
    {
        $r = $jy <= 979 ? 621 : 1600;
        if ($jy <= 979) {
            $t = $jy;
        } else {
            $t = $jy - 979;
        }
        $a = (int) (365 * $t + intdiv($t, 33) * 8 + intdiv($t % 33 + 3, 4)) + 78 + $jd;
        if ($jm < 7) {
            $a += 31 * ($jm - 1);
        } else {
            $a += 30 * ($jm - 7) + 186;
        }
        $r += 400 * intdiv($a, 146097);
        $a %= 146097;
        if ($a > 36524) {
            $a--;
            $r += 100 * intdiv($a, 36524);
            $a %= 36524;
            if ($a >= 365) {
                $a++;
            }
        }
        $r += 4 * intdiv($a, 1461);
        $a %= 1461;
        $r += intdiv($a - 1, 365);
        if ($a > 365) {
            $a = ($a - 1) % 365;
        }
        $s = $a + 1;
        $leap = ($r % 4 === 0 && $r % 100 !== 0) || ($r % 400 === 0) ? 29 : 28;
        $o = [0, 31, $leap, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $i = 0;
        for ($i = 0; $i < 13; $i++) {
            $u = $o[$i];
            if ($s <= $u) {
                break;
            }
            $s -= $u;
        }

        return [$r, $i, $s];
    }

    /**
     * @return array{0:int,1:int,2:int} Jalali year, month (1–12), day
     */
    public static function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $r = $gy <= 1600 ? 0 : 979;
        if ($gy <= 1600) {
            $t = $gy - 621;
        } else {
            $t = $gy - 1600;
        }
        $gdm = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $a = $gm > 2 ? $t + 1 : $t;
        $i = (int) (365 * $t + intdiv($a + 3, 4) - intdiv($a + 99, 100) + intdiv($a + 399, 400) - 80 + $gd + $gdm[$gm - 1]);
        $r += 33 * intdiv($i, 12053);
        $i %= 12053;
        $r += 4 * intdiv($i, 1461);
        $i %= 1461;
        $r += intdiv($i - 1, 365);
        if ($i > 365) {
            $i = ($i - 1) % 365;
        }
        $jm = $i < 186 ? 1 + intdiv($i, 31) : 7 + intdiv($i - 186, 30);
        $jd = 1 + ($i < 186 ? $i % 31 : ($i - 186) % 30);

        return [$r, $jm, $jd];
    }

    public static function parseShamsiDateStartOfDay(?string $value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $value = trim($value);
        if (! preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/', $value, $m)) {
            throw new InvalidArgumentException('فرمت تاریخ شمسی باید به صورت سال/ماه/روز باشد (مثلاً ۱۴۰۳/۰۱/۱۵).');
        }
        $jy = (int) $m[1];
        $jm = (int) $m[2];
        $jd = (int) $m[3];
        if ($jm < 1 || $jm > 12 || $jd < 1 || $jd > 31) {
            throw new InvalidArgumentException('تاریخ شمسی نامعتبر است.');
        }
        [$gy, $gm, $gd] = self::jalaliToGregorian($jy, $jm, $jd);

        return Carbon::create($gy, $gm, $gd, 0, 0, 0);
    }

    public static function parseShamsiDateEndOfDay(?string $value): ?Carbon
    {
        $c = self::parseShamsiDateStartOfDay($value);

        return $c?->endOfDay();
    }

    public static function formatShamsiDate(?Carbon $date): string
    {
        if ($date === null) {
            return '';
        }
        [$jy, $jm, $jd] = self::gregorianToJalali(
            (int) $date->year,
            (int) $date->month,
            (int) $date->day
        );

        return sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
    }

    /** Current Jalali year (e.g. 1404) for datepicker yearRange. */
    public static function currentJalaliYear(): int
    {
        [$jy] = self::gregorianToJalali(
            (int) now()->year,
            (int) now()->month,
            (int) now()->day
        );

        return $jy;
    }

    /** Shamsi date + 24h clock (Gregorian time digits, common in Iran admin UIs). */
    public static function formatShamsiDateTime(?Carbon $date): string
    {
        if ($date === null) {
            return '—';
        }

        return self::formatShamsiDate($date).' '.$date->format('H:i');
    }
}
