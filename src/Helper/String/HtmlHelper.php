<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\String;

use Symfony\Component\HtmlSanitizer\{
    HtmlSanitizer,
    HtmlSanitizerConfig
};

final class HtmlHelper
{
    /**
     * @param list<string> $allowed e.g. ['p', 'a', 'br']
     */
    public static function stripTags(string $html, array $allowed = []): string
    {
        $allowedString = '';
        foreach ($allowed as $tag) {
            $allowedString .= '<' . $tag . '>';
        }

        return strip_tags($html, $allowedString);
    }

    public static function sanitize(string $html): string
    {
        if (class_exists(HtmlSanitizer::class)) {
            $config = new HtmlSanitizerConfig;
            $config = $config->allowSafeElements();
            $sanitizer = new HtmlSanitizer($config);

            return $sanitizer->sanitize($html);
        }

        return self::stripTags($html, ['p', 'a', 'br', 'strong', 'em', 'ul', 'ol', 'li', 'span', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']);
    }

    public static function truncatePreservingTags(string $html, int $length): string
    {
        if (mb_strlen(strip_tags($html)) <= $length) {
            return $html;
        }

        $result = '';
        $totalLength = 0;
        $openTags = [];

        $tokens = preg_split('~(<[^>]+>)~u', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];

        foreach ($tokens as $token) {
            if (str_starts_with($token, '<')) {
                if (preg_match('~^</(\w+)>~', $token, $matches)) {
                    array_pop($openTags);
                } elseif (preg_match('~^<(\w+)[^/>]*>$~', $token, $matches) && !in_array($matches[1], ['br', 'img', 'hr', 'input'], true)) {
                    $openTags[] = $matches[1];
                }
                $result .= $token;
            } else {
                $remaining = $length - $totalLength;
                if (mb_strlen($token) <= $remaining) {
                    $result .= $token;
                    $totalLength += mb_strlen($token);
                } else {
                    $result .= mb_substr($token, 0, $remaining) . '…';

                    break;
                }
            }
        }

        while ($openTags !== []) {
            $tag = array_pop($openTags);
            $result .= '</' . $tag . '>';
        }

        return $result;
    }

    public static function extractText(string $html): string
    {
        $html = preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is', ' ', $html) ?? $html;
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('~\s+~u', ' ', $text) ?? $text;

        return mb_trim($text);
    }

    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function unescape(string $value): string
    {
        return htmlspecialchars_decode($value, ENT_QUOTES | ENT_HTML5);
    }

    public static function autoLink(string $text): string
    {
        return preg_replace_callback(
            '~(https?://[^\s<>"\']+)~u',
            static fn (array $m): string => '<a href="' . self::escape($m[1]) . '">' . self::escape($m[1]) . '</a>',
            $text
        ) ?? $text;
    }

    public static function nl2br(string $text): string
    {
        return nl2br(self::escape($text), false);
    }

    public static function highlightKeyword(string $text, string $keyword, string $tag = 'mark'): string
    {
        if ($keyword === '') {
            return $text;
        }

        $pattern = '~(' . preg_quote($keyword, '~') . ')~iu';

        return preg_replace($pattern, '<' . $tag . '>$1</' . $tag . '>', $text) ?? $text;
    }
}
