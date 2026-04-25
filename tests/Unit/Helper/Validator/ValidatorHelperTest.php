<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Validator;

use Danilovl\HelperUtils\Helper\Validator\ValidatorHelper;
use PHPUnit\Framework\TestCase;
use ArrayObject;

final class ValidatorHelperTest extends TestCase
{
    public function testIsEmail(): void
    {
        self::assertTrue(ValidatorHelper::isEmail('test@example.com'));
        self::assertTrue(ValidatorHelper::isEmail('john.doe+tag@example.co.uk'));
        self::assertFalse(ValidatorHelper::isEmail('not an email'));
        self::assertFalse(ValidatorHelper::isEmail('@example.com'));
    }

    public function testIsUrl(): void
    {
        self::assertTrue(ValidatorHelper::isUrl('https://example.com'));
        self::assertTrue(ValidatorHelper::isUrl('http://example.com/path?q=1'));
        self::assertFalse(ValidatorHelper::isUrl('not a url'));
    }

    public function testIsUuid(): void
    {
        self::assertTrue(ValidatorHelper::isUuid('550e8400-e29b-41d4-a716-446655440000'));
        self::assertFalse(ValidatorHelper::isUuid('not a uuid'));
        self::assertFalse(ValidatorHelper::isUuid('550e8400-e29b-01d4-a716-446655440000'));
    }

    public function testIsIp(): void
    {
        self::assertTrue(ValidatorHelper::isIp('192.168.1.1'));
        self::assertTrue(ValidatorHelper::isIp('::1'));
        self::assertFalse(ValidatorHelper::isIp('not an ip'));
    }

    public function testIsJson(): void
    {
        self::assertTrue(ValidatorHelper::isJson('{"a":1}'));
        self::assertTrue(ValidatorHelper::isJson('[1,2,3]'));
        self::assertTrue(ValidatorHelper::isJson('null'));
        self::assertFalse(ValidatorHelper::isJson(''));
        self::assertFalse(ValidatorHelper::isJson('{invalid}'));
    }

    public function testIsBase64(): void
    {
        self::assertTrue(ValidatorHelper::isBase64(base64_encode('hello')));
        self::assertTrue(ValidatorHelper::isBase64(base64_encode('a longer string for encoding')));
        self::assertFalse(ValidatorHelper::isBase64('not base 64!@#'));
        self::assertFalse(ValidatorHelper::isBase64(''));
    }

    public function testIsHexColor(): void
    {
        self::assertTrue(ValidatorHelper::isHexColor('#ff0000'));
        self::assertTrue(ValidatorHelper::isHexColor('ff0000'));
        self::assertTrue(ValidatorHelper::isHexColor('#f00'));
        self::assertFalse(ValidatorHelper::isHexColor('#ggg'));
        self::assertFalse(ValidatorHelper::isHexColor('not a color'));
    }

    public function testIsCreditCardValid(): void
    {
        // Valid Luhn-checkable test numbers
        self::assertTrue(ValidatorHelper::isCreditCard('4532015112830366')); // Visa test
        self::assertTrue(ValidatorHelper::isCreditCard('5425233430109903')); // MasterCard test
        self::assertTrue(ValidatorHelper::isCreditCard('4532-0151-1283-0366')); // with dashes
    }

    public function testIsCreditCardInvalid(): void
    {
        self::assertFalse(ValidatorHelper::isCreditCard('1234567890123456'));
        self::assertFalse(ValidatorHelper::isCreditCard('123'));
        self::assertFalse(ValidatorHelper::isCreditCard(''));
    }

    public function testIsIban(): void
    {
        self::assertTrue(ValidatorHelper::isIban('GB82 WEST 1234 5698 7654 32'));
        self::assertTrue(ValidatorHelper::isIban('DE89370400440532013000'));
        self::assertFalse(ValidatorHelper::isIban('not an iban'));
        self::assertFalse(ValidatorHelper::isIban('GB00 INVALID'));
    }

    public function testViolationsToArrayEmpty(): void
    {
        $empty = new ArrayObject([]);
        self::assertSame([], ValidatorHelper::violationsToArray($empty));
    }

    public function testViolationsToArrayWithFakeViolations(): void
    {
        $violation = new class() {
            public function getPropertyPath(): string { return 'email'; }

            public function getMessage(): string { return 'Invalid email'; }
        };
        $list = new ArrayObject([$violation]);
        $result = ValidatorHelper::violationsToArray($list);
        self::assertSame(['email' => ['Invalid email']], $result);
    }
}
