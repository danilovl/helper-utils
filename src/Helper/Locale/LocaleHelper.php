<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Locale;

use Locale;

final class LocaleHelper
{
    /** ISO 639-1 codes of right-to-left languages. */
    private const array RTL_LANGUAGES = ['ar', 'fa', 'he', 'iw', 'ur', 'ps', 'sd', 'yi', 'dv', 'ku'];

    /**
     * Parses an Accept-Language header into an array sorted by quality, highest first.
     *
     * @return list<array{locale: string, quality: float}>
     */
    public static function parseAcceptLanguage(string $header): array
    {
        if (mb_trim($header) === '') {
            return [];
        }

        $entries = [];
        foreach (explode(',', $header) as $part) {
            $part = mb_trim($part);
            if ($part === '') {
                continue;
            }
            $segments = explode(';', $part);
            $locale = mb_trim($segments[0]);
            if ($locale === '') {
                continue;
            }
            $quality = 1.0;
            foreach (array_slice($segments, 1) as $modifier) {
                if (preg_match('~q\s*=\s*([\d.]+)~', $modifier, $m)) {
                    $quality = (float) $m[1];
                }
            }
            $entries[] = ['locale' => $locale, 'quality' => $quality];
        }

        usort($entries, static fn (array $a, array $b): int => $b['quality'] <=> $a['quality']);

        return $entries;
    }

    public static function getLanguage(string $locale): string
    {
        if (class_exists(Locale::class)) {
            $language = Locale::getPrimaryLanguage($locale);
            if (!empty($language)) {
                return $language;
            }
        }
        $parts = preg_split('~[_\-]~', $locale, 2) ?: [$locale];

        return mb_strtolower($parts[0]);
    }

    public static function getRegion(string $locale): ?string
    {
        if (class_exists(Locale::class)) {
            $region = Locale::getRegion($locale);
            if (is_string($region) && $region !== '') {
                return $region;
            }
        }
        $parts = preg_split('~[_\-]~', $locale);
        if ($parts === false || !isset($parts[1]) || $parts[1] === '') {
            return null;
        }

        return mb_strtoupper($parts[1]);
    }

    public static function isValid(string $locale): bool
    {
        if (mb_trim($locale) === '') {
            return false;
        }

        return preg_match('~^[a-zA-Z]{2,3}([_\-][a-zA-Z]{2,4})?([_\-][a-zA-Z]{2,3})?$~', $locale) === 1;
    }

    public static function getDisplayName(string $locale, string $inLocale = 'en'): string
    {
        if (class_exists(Locale::class)) {
            $name = Locale::getDisplayName($locale, $inLocale);
            if ($name !== '') {
                return $name;
            }
        }

        return $locale;
    }

    public static function isRtl(string $locale): bool
    {
        $language = mb_strtolower(self::getLanguage($locale));

        return in_array($language, self::RTL_LANGUAGES, true);
    }
}
