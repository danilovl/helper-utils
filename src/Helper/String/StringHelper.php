<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\String;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;
use InvalidArgumentException;

final class StringHelper
{
    public static function truncate(string $value, int $length, string $suffix = '…'): string
    {
        if (mb_strlen($value) <= $length) {
            return $value;
        }

        $cutAt = max(0, $length - mb_strlen($suffix));

        return mb_substr($value, 0, $cutAt) . $suffix;
    }

    public static function truncateWords(string $value, int $words, string $suffix = '…'): string
    {
        $parts = preg_split('~\s+~u', mb_trim($value)) ?: [];
        if (count($parts) <= $words) {
            return $value;
        }

        return implode(' ', array_slice($parts, 0, $words)) . $suffix;
    }

    public static function startsWith(string $haystack, string $needle): bool
    {
        return str_starts_with($haystack, $needle);
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        return str_ends_with($haystack, $needle);
    }

    public static function contains(string $haystack, string $needle, bool $caseInsensitive = false): bool
    {
        if ($needle === '') {
            return true;
        }
        if ($caseInsensitive) {
            return mb_stripos($haystack, $needle) !== false;
        }

        return str_contains($haystack, $needle);
    }

    /**
     * @param list<string> $needles
     */
    public static function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (self::contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function camelToSnake(string $value): string
    {
        $result = preg_replace('~([a-z\d])([A-Z])~', '$1_$2', $value) ?? $value;
        $result = preg_replace('~([A-Z]+)([A-Z][a-z])~', '$1_$2', $result) ?? $result;

        return mb_strtolower($result);
    }

    public static function snakeToCamel(string $value, bool $upperFirst = false): string
    {
        $parts = explode('_', $value);
        $first = array_shift($parts);
        $first = $upperFirst ? ucfirst($first) : $first;
        $result = $first;
        foreach ($parts as $part) {
            $result .= ucfirst($part);
        }

        return $result;
    }

    public static function kebabToCamel(string $value): string
    {
        return self::snakeToCamel(str_replace('-', '_', $value));
    }

    public static function pascalCase(string $value): string
    {
        return self::snakeToCamel(str_replace('-', '_', $value), true);
    }

    public static function slugify(string $value, string $separator = '-'): string
    {
        $slugger = new AsciiSlugger;

        return mb_strtolower((string) $slugger->slug($value, $separator));
    }

    public static function removeAccents(string $value): string
    {
        $slugger = new AsciiSlugger;

        return (string) $slugger->slug($value, ' ');
    }

    public static function transliterate(string $value): string
    {
        $result = transliterator_transliterate('Any-Latin; Latin-ASCII', $value);

        return $result !== false ? $result : $value;
    }

    public static function random(int $length, string $alphabet = 'alphanumeric'): string
    {
        $chars = match ($alphabet) {
            'numeric' => '0123456789',
            'alpha' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'hex' => '0123456789abcdef',
            default => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        };
        $max = mb_strlen($chars) - 1;
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }

        return $result;
    }

    public static function uuid(): string
    {
        return Uuid::v4()->toRfc4122();
    }

    public static function mask(string $value, int $visibleStart = 4, int $visibleEnd = 4, string $maskChar = '*'): string
    {
        $length = mb_strlen($value);
        if ($length <= $visibleStart + $visibleEnd) {
            return str_repeat($maskChar, $length);
        }

        $start = mb_substr($value, 0, $visibleStart);
        $end = $visibleEnd > 0 ? mb_substr($value, -$visibleEnd) : '';
        $maskedLength = $length - $visibleStart - $visibleEnd;

        return $start . str_repeat($maskChar, $maskedLength) . $end;
    }

    public static function maskEmail(string $email): string
    {
        $atPos = mb_strrpos($email, '@');
        if ($atPos === false) {
            return self::mask($email, 1, 1);
        }
        $local = mb_substr($email, 0, $atPos);
        $domain = mb_substr($email, $atPos);

        $localLen = mb_strlen($local);
        if ($localLen <= 2) {
            $maskedLocal = str_repeat('*', $localLen);
        } else {
            $maskedLocal = mb_substr($local, 0, 1)
                . str_repeat('*', $localLen - 2)
                . mb_substr($local, -1);
        }

        return $maskedLocal . $domain;
    }

    public static function maskPhone(string $phone, int $visibleEnd = 4): string
    {
        $length = mb_strlen($phone);
        if ($length <= $visibleEnd) {
            return $phone;
        }
        $masked = '';
        $maskCount = $length - $visibleEnd;
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($phone, $i, 1);
            if ($i < $maskCount && preg_match('~\d~', $char)) {
                $masked .= '*';
            } else {
                $masked .= $char;
            }
        }

        return $masked;
    }

    public static function reverse(string $value): string
    {
        $chars = mb_str_split($value);

        return implode('', array_reverse($chars));
    }

    public static function length(string $value): int
    {
        return mb_strlen($value);
    }

    public static function wordCount(string $value): int
    {
        $value = mb_trim($value);
        if ($value === '') {
            return 0;
        }
        $parts = preg_split('~\s+~u', $value);

        return $parts === false ? 0 : count($parts);
    }

    public static function readingTime(string $value, int $wordsPerMinute = 200): int
    {
        if ($wordsPerMinute < 1) {
            throw new InvalidArgumentException('wordsPerMinute must be at least 1.');
        }

        return (int) max(1, ceil(self::wordCount($value) / $wordsPerMinute));
    }

    public static function levenshtein(string $a, string $b): int
    {
        return levenshtein($a, $b);
    }

    public static function similarity(string $a, string $b): float
    {
        if ($a === '' && $b === '') {
            return 1.0;
        }
        similar_text($a, $b, $percent);

        return $percent / 100;
    }

    public static function pluralize(string $word): string
    {
        $rules = [
            '~(quiz)$~i' => '$1zes',
            '~^(ox)$~i' => '$1en',
            '~([m|l])ouse$~i' => '$1ice',
            '~(matr|vert|ind)ix|ex$~i' => '$1ices',
            '~(x|ch|ss|sh)$~i' => '$1es',
            '~([^aeiouy]|qu)y$~i' => '$1ies',
            '~(hive)$~i' => '$1s',
            '~(?:([^f])fe|([lr])f)$~i' => '$1$2ves',
            '~sis$~i' => 'ses',
            '~([ti])um$~i' => '$1a',
            '~(buffal|tomat)o$~i' => '$1oes',
            '~(bu)s$~i' => '$1ses',
            '~(alias|status)$~i' => '$1es',
            '~(octop|vir)us$~i' => '$1i',
            '~s$~i' => 's',
            '~$~' => 's',
        ];

        foreach ($rules as $pattern => $replacement) {
            if (preg_match($pattern, $word)) {
                return preg_replace($pattern, $replacement, $word) ?? $word;
            }
        }

        return $word;
    }

    public static function singularize(string $word): string
    {
        $rules = [
            '~(quiz)zes$~i' => '$1',
            '~(matr)ices$~i' => '$1ix',
            '~(vert|ind)ices$~i' => '$1ex',
            '~^(ox)en$~i' => '$1',
            '~(alias|status)(es)?$~i' => '$1',
            '~(octop|vir)(us|i)$~i' => '$1us',
            '~^(a)x[ie]s$~i' => '$1xis',
            '~(cris|test)(is|es)$~i' => '$1is',
            '~(shoe)s$~i' => '$1',
            '~(o)es$~i' => '$1',
            '~(bus)(es)?$~i' => '$1',
            '~([m|l])ice$~i' => '$1ouse',
            '~(x|ch|ss|sh)es$~i' => '$1',
            '~(m)ovies$~i' => '$1ovie',
            '~(s)eries$~i' => '$1eries',
            '~([^aeiouy]|qu)ies$~i' => '$1y',
            '~([lr])ves$~i' => '$1f',
            '~(tive)s$~i' => '$1',
            '~(hive)s$~i' => '$1',
            '~(li|wi|kni)ves$~i' => '$1fe',
            '~(^analy)(sis|ses)$~i' => '$1sis',
            '~((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)(sis|ses)$~i' => '$1$2sis',
            '~([ti])a$~i' => '$1um',
            '~(n)ews$~i' => '$1ews',
            '~(h|bl)ouses$~i' => '$1ouse',
            '~(corpse)s$~i' => '$1',
            '~(us)es$~i' => '$1',
            '~s$~i' => '',
        ];

        foreach ($rules as $pattern => $replacement) {
            if (preg_match($pattern, $word)) {
                return preg_replace($pattern, $replacement, $word) ?? $word;
            }
        }

        return $word;
    }
}
