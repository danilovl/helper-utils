<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Service;

use Danilovl\HelperUtils\Service\RequestHelper;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\{HeaderBag, ParameterBag, Request, RequestStack};

#[AllowMockObjectsWithoutExpectations]
final class RequestHelperTest extends TestCase
{
    /** @var MockObject&RequestStack */
    private RequestStack $requestStack;

    /** @var MockObject&Request */
    private Request $request;

    private RequestHelper $helper;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->request = $this->createMock(Request::class);
        $this->request->headers = new HeaderBag;
        $this->request->attributes = new ParameterBag;

        $this->helper = new RequestHelper($this->requestStack);
    }

    public function testGetCurrentRequest(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);
        self::assertSame($this->request, $this->helper->getCurrentRequest());
    }

    public function testGetClientIp(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);
        $this->request->method('getClientIp')->willReturn('127.0.0.1');

        self::assertSame('127.0.0.1', $this->helper->getClientIp());
    }

    public function testIsAjax(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);
        $this->request->method('isXmlHttpRequest')->willReturn(true);

        self::assertTrue($this->helper->isAjax());
    }

    public function testIsMobile(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);
        $this->request->headers->set('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1');

        self::assertTrue($this->helper->isMobile());

        $this->request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
        self::assertFalse($this->helper->isMobile());
    }

    public function testGetReferer(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);
        $this->request->headers->set('referer', 'https://example.com');

        self::assertSame('https://example.com', $this->helper->getReferer());
    }

    public function testGetAcceptedLanguage(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);
        $this->request->method('getPreferredLanguage')->willReturn('en');

        self::assertSame('en', $this->helper->getAcceptedLanguage());
    }

    public function testGetCurrentUrl(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);
        $this->request->method('getUri')->willReturn('https://example.com/path?query=1');
        $this->request->method('getSchemeAndHttpHost')->willReturn('https://example.com');
        $this->request->method('getPathInfo')->willReturn('/path');

        self::assertSame('https://example.com/path?query=1', $this->helper->getCurrentUrl(true));
        self::assertSame('https://example.com/path', $this->helper->getCurrentUrl(false));
    }

    public function testGetRouteName(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);
        $this->request->attributes->set('_route', 'app_homepage');

        self::assertSame('app_homepage', $this->helper->getRouteName());
    }
}
