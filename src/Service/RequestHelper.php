<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Service;

use Symfony\Component\HttpFoundation\{Request, RequestStack};

final readonly class RequestHelper
{
    public function __construct(private RequestStack $requestStack) {}

    public function getCurrentRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function getClientIp(): ?string
    {
        return $this->getCurrentRequest()?->getClientIp();
    }

    public function isAjax(): bool
    {
        return $this->getCurrentRequest()?->isXmlHttpRequest() ?? false;
    }

    /**
     * Heuristic mobile detection based on User-Agent.
     */
    public function isMobile(): bool
    {
        $ua = $this->getUserAgent();
        if ($ua === null) {
            return false;
        }

        return preg_match('~(android|iphone|ipod|blackberry|windows phone|opera mini|mobile|webos)~i', $ua) === 1;
    }

    public function getReferer(): ?string
    {
        $request = $this->getCurrentRequest();
        if ($request === null) {
            return null;
        }
        $referer = $request->headers->get('referer');

        return $referer !== '' ? $referer : null;
    }

    public function getUserAgent(): ?string
    {
        return $this->getCurrentRequest()?->headers->get('User-Agent');
    }

    public function getAcceptedLanguage(): ?string
    {
        $request = $this->getCurrentRequest();

        return $request?->getPreferredLanguage();
    }

    public function getCurrentUrl(bool $withQuery = true): ?string
    {
        $request = $this->getCurrentRequest();
        if ($request === null) {
            return null;
        }

        return $withQuery ? $request->getUri() : $request->getSchemeAndHttpHost() . $request->getPathInfo();
    }

    public function getRouteName(): ?string
    {
        $request = $this->getCurrentRequest();
        if ($request === null) {
            return null;
        }
        $route = $request->attributes->get('_route');

        return is_string($route) ? $route : null;
    }
}
