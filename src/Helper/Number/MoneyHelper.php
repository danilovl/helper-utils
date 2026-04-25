<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Number;

use NumberFormatter;

final class MoneyHelper
{
    /**
     * Currencies whose minor unit is not 1/100 of the major unit.
     * Most common ones; not exhaustive. Default exponent is 2.
     */
    private const array CURRENCY_EXPONENT = [
        'JPY' => 0,
        'KRW' => 0,
        'VND' => 0,
        'CLP' => 0,
        'ISK' => 0,
        'BIF' => 0,
        'XAF' => 0,
        'XOF' => 0,
        'XPF' => 0,
        'KWD' => 3,
        'BHD' => 3,
        'OMR' => 3,
        'JOD' => 3,
        'IQD' => 3,
        'LYD' => 3,
        'TND' => 3,
    ];

    public static function format(int $amountMinor, string $currency = 'USD', string $locale = 'en'): string
    {
        $major = self::minorToMajor($amountMinor, $currency);

        if (class_exists(NumberFormatter::class)) {
            $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
            $result = $formatter->formatCurrency($major, mb_strtoupper($currency));
            /** @phpstan-ignore function.alreadyNarrowedType */
            if (is_string($result)) {
                return $result;
            }
        }

        $exponent = self::getExponent($currency);

        return mb_strtoupper($currency) . ' ' . number_format($major, $exponent);
    }

    public static function minorToMajor(int $amountMinor, string $currency = 'USD'): float
    {
        $exponent = self::getExponent($currency);

        return $amountMinor / (10 ** $exponent);
    }

    public static function majorToMinor(float $amountMajor, string $currency = 'USD'): int
    {
        $exponent = self::getExponent($currency);

        return (int) round($amountMajor * (10 ** $exponent));
    }

    public static function getCurrencySymbol(string $currency, string $locale = 'en'): string
    {
        if (class_exists(NumberFormatter::class)) {
            $formatter = new NumberFormatter($locale . '@currency=' . mb_strtoupper($currency), NumberFormatter::CURRENCY);
            $symbol = $formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
            if (is_string($symbol) && $symbol !== '') {
                return $symbol;
            }
        }

        return mb_strtoupper($currency);
    }

    private static function getExponent(string $currency): int
    {
        return self::CURRENCY_EXPONENT[mb_strtoupper($currency)] ?? 2;
    }
}
