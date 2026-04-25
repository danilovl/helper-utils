<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\String;

final class TextHelper
{
    public static function generateExcerpt(string $text, int $maxLength = 200): string
    {
        $clean = mb_trim(preg_replace('~\s+~u', ' ', $text) ?? $text);
        if (mb_strlen($clean) <= $maxLength) {
            return $clean;
        }

        $cut = mb_substr($clean, 0, $maxLength);
        $lastSpace = mb_strrpos($cut, ' ');

        if ($lastSpace !== false && $lastSpace > 0) {
            $cut = mb_substr($cut, 0, $lastSpace);
        }

        return $cut . '…';
    }

    public static function highlight(string $text, string $needle, string $before = '<mark>', string $after = '</mark>'): string
    {
        if ($needle === '') {
            return $text;
        }

        $pattern = '~(' . preg_quote($needle, '~') . ')~iu';

        return preg_replace($pattern, $before . '$1' . $after, $text) ?? $text;
    }

    public static function lineCount(string $text): int
    {
        if ($text === '') {
            return 0;
        }

        return mb_substr_count($text, "\n") + 1;
    }

    public static function indent(string $text, int $spaces = 4): string
    {
        $prefix = str_repeat(' ', max(0, $spaces));
        $lines = explode("\n", $text);
        $lines = array_map(static fn (string $line): string => $line === '' ? $line : $prefix . $line, $lines);

        return implode("\n", $lines);
    }

    public static function dedent(string $text): string
    {
        $lines = explode("\n", $text);
        $minIndent = null;

        foreach ($lines as $line) {
            if (mb_trim($line) === '') {
                continue;
            }
            if (preg_match('~^( +)~', $line, $matches)) {
                $len = mb_strlen($matches[1]);
                if ($minIndent === null || $len < $minIndent) {
                    $minIndent = $len;
                }
            } else {
                $minIndent = 0;

                break;
            }
        }

        if ($minIndent === null || $minIndent === 0) {
            return $text;
        }

        $result = [];
        foreach ($lines as $line) {
            $result[] = mb_trim($line) === '' ? $line : mb_substr($line, $minIndent);
        }

        return implode("\n", $result);
    }
}
