<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Network;

final class UrlHelper
{
    public static function isAbsolute(string $url): bool
    {
        return preg_match('~^[a-z][a-z0-9+\-.]*://~i', $url) === 1
            || str_starts_with($url, '//');
    }

    public static function isValid(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function getDomain(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) && $host !== '' ? $host : null;
    }

    /**
     * Returns the registrable root domain (e.g. example.com from foo.bar.example.com).
     * This is a heuristic — for true public-suffix-list support, use a dedicated library.
     */
    public static function getRootDomain(string $url): ?string
    {
        $host = self::getDomain($url);
        if ($host === null) {
            return null;
        }
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return $host;
        }
        $parts = explode('.', $host);
        $count = count($parts);
        if ($count <= 2) {
            return $host;
        }

        $multiPartTlds = ['co.uk', 'com.au', 'co.jp', 'com.br', 'co.nz', 'co.za', 'org.uk', 'gov.uk', 'ac.uk'];
        $lastTwo = $parts[$count - 2] . '.' . $parts[$count - 1];
        if (in_array($lastTwo, $multiPartTlds, true)) {
            return $parts[$count - 3] . '.' . $lastTwo;
        }

        return $parts[$count - 2] . '.' . $parts[$count - 1];
    }

    public static function getScheme(string $url): ?string
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);

        return is_string($scheme) ? $scheme : null;
    }

    public static function isExternal(string $url, string $currentHost): bool
    {
        $host = self::getDomain($url);
        if ($host === null) {
            return false;
        }

        return strcasecmp($host, $currentHost) !== 0;
    }

    /**
     * @param array<string, scalar> $params
     */
    public static function addQueryParams(string $url, array $params): string
    {
        $parts = parse_url($url);
        if ($parts === false) {
            return $url;
        }

        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        $merged = array_merge($query, $params);
        $parts['query'] = http_build_query($merged);

        return self::buildUrlFromParts($parts);
    }

    public static function removeQueryParam(string $url, string $param): string
    {
        $parts = parse_url($url);
        if ($parts === false || !isset($parts['query'])) {
            return $url;
        }
        parse_str($parts['query'], $query);
        unset($query[$param]);
        $parts['query'] = http_build_query($query);
        if ($parts['query'] === '') {
            unset($parts['query']);
        }

        return self::buildUrlFromParts($parts);
    }

    public static function getQueryParam(string $url, string $param): ?string
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!is_string($query)) {
            return null;
        }
        $parsed = [];
        parse_str($query, $parsed);
        $value = $parsed[$param] ?? null;

        return is_string($value) ? $value : null;
    }

    public static function makeAbsolute(string $url, string $baseUrl): string
    {
        if (self::isAbsolute($url)) {
            return $url;
        }

        $baseParts = parse_url($baseUrl);
        if (!isset($baseParts['scheme'], $baseParts['host'])) {
            return $url;
        }
        $base = $baseParts['scheme'] . '://' . $baseParts['host'];
        if (isset($baseParts['port'])) {
            $base .= ':' . $baseParts['port'];
        }

        if (str_starts_with($url, '/')) {
            return $base . $url;
        }
        $basePath = isset($baseParts['path']) ? mb_rtrim((string) $baseParts['path'], '/') : '';

        return $base . $basePath . '/' . mb_ltrim($url, '/');
    }

    public static function ensureTrailingSlash(string $url): string
    {
        return mb_rtrim($url, '/') . '/';
    }

    public static function removeTrailingSlash(string $url): string
    {
        return mb_rtrim($url, '/');
    }

    /**
     * @param array<int|string, mixed> $params
     */
    public static function buildQueryString(array $params): string
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param array<string, mixed> $parts
     */
    private static function buildUrlFromParts(array $parts): string
    {
        $url = '';
        if (isset($parts['scheme']) && is_string($parts['scheme'])) {
            $url .= $parts['scheme'] . '://';
        }
        if (isset($parts['user']) && is_string($parts['user'])) {
            $url .= $parts['user'];
            if (isset($parts['pass']) && is_string($parts['pass'])) {
                $url .= ':' . $parts['pass'];
            }
            $url .= '@';
        }
        if (isset($parts['host']) && is_string($parts['host'])) {
            $url .= $parts['host'];
        }
        if (isset($parts['port']) && (is_int($parts['port']) || is_string($parts['port']))) {
            $url .= ':' . $parts['port'];
        }
        if (isset($parts['path']) && is_string($parts['path'])) {
            $url .= $parts['path'];
        }
        if (isset($parts['query']) && is_string($parts['query']) && $parts['query'] !== '') {
            $url .= '?' . $parts['query'];
        }
        if (isset($parts['fragment']) && is_string($parts['fragment'])) {
            $url .= '#' . $parts['fragment'];
        }

        return $url;
    }
}
