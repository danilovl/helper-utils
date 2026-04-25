<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Validator;

use JsonException;

final class ValidatorHelper
{
    /**
     * @return array<string, list<string>>
     */
    public static function violationsToArray(object $violations): array
    {
        if (!is_iterable($violations)) {
            return [];
        }

        $result = [];
        foreach ($violations as $violation) {
            if (!is_object($violation) || !method_exists($violation, 'getPropertyPath') || !method_exists($violation, 'getMessage')) {
                continue;
            }
            $path = (string) $violation->getPropertyPath();
            $message = (string) $violation->getMessage();
            $result[$path][] = $message;
        }

        return $result;
    }

    public static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isUrl(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public static function isUuid(string $value): bool
    {
        return preg_match('~^[0-9a-f]{8}-[0-9a-f]{4}-[1-7][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i', $value) === 1;
    }

    public static function isIp(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    public static function isJson(string $value): bool
    {
        if (mb_trim($value) === '') {
            return false;
        }

        try {
            json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return false;
        }

        return true;
    }

    public static function isBase64(string $value): bool
    {
        if ($value === '' || mb_strlen($value) % 4 !== 0) {
            return false;
        }
        if (preg_match('~^[A-Za-z0-9+/]*={0,2}$~', $value) !== 1) {
            return false;
        }
        $decoded = base64_decode($value, true);

        return $decoded !== false && base64_encode($decoded) === $value;
    }

    public static function isHexColor(string $value): bool
    {
        return preg_match('~^#?([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$~', $value) === 1;
    }

    public static function isCreditCard(string $value): bool
    {
        $digits = preg_replace('~\D~', '', $value) ?? '';
        if (mb_strlen($digits) < 13 || mb_strlen($digits) > 19) {
            return false;
        }

        $sum = 0;
        $shouldDouble = false;
        for ($i = mb_strlen($digits) - 1; $i >= 0; $i--) {
            $n = (int) $digits[$i];
            if ($shouldDouble) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $shouldDouble = !$shouldDouble;
        }

        return $sum % 10 === 0;
    }

    public static function isIban(string $value): bool
    {
        $value = mb_strtoupper(preg_replace('~\s+~', '', $value) ?? '');
        if (!preg_match('~^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$~', $value)) {
            return false;
        }

        $rearranged = mb_substr($value, 4) . mb_substr($value, 0, 4);
        $numeric = '';
        foreach (mb_str_split($rearranged) as $char) {
            $numeric .= ctype_alpha($char) ? (string) (ord($char) - 55) : $char;
        }

        return self::mod97($numeric) === 1;
    }

    /**
     * Computes mod 97 of a numeric string of arbitrary length without bcmath.
     */
    private static function mod97(string $numeric): int
    {
        $remainder = 0;
        $length = mb_strlen($numeric);
        for ($i = 0; $i < $length; $i++) {
            $remainder = ($remainder * 10 + (int) $numeric[$i]) % 97;
        }

        return $remainder;
    }
}
