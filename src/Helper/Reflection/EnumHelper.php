<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Reflection;

use BackedEnum;
use Danilovl\HelperUtils\Exception\HelperException;
use UnitEnum;

final class EnumHelper
{
    /**
     * @template T of BackedEnum
     * @param class-string<T> $enum
     * @return list<int|string>
     */
    public static function values(string $enum): array
    {
        self::assertBackedEnum($enum);
        $cases = $enum::cases();
        $result = [];
        foreach ($cases as $case) {
            $result[] = $case->value;
        }

        return $result;
    }

    /**
     * @template T of UnitEnum
     * @param class-string<T> $enum
     * @return list<string>
     */
    public static function names(string $enum): array
    {
        self::assertEnum($enum);
        $result = [];
        foreach ($enum::cases() as $case) {
            $result[] = $case->name;
        }

        return $result;
    }

    /**
     * @template T of UnitEnum
     * @param class-string<T> $enum
     * @return T|null
     */
    public static function tryFromName(string $enum, string $name): ?UnitEnum
    {
        self::assertEnum($enum);
        foreach ($enum::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    /**
     * @param class-string<UnitEnum> $enum
     */
    public static function exists(string $enum, string $name): bool
    {
        return self::tryFromName($enum, $name) !== null;
    }

    /**
     * @template T of UnitEnum
     * @param class-string<T> $enum
     * @return T
     */
    public static function random(string $enum): UnitEnum
    {
        self::assertEnum($enum);
        $cases = $enum::cases();
        if ($cases === []) {
            throw new HelperException(sprintf('Enum "%s" has no cases.', $enum));
        }

        return $cases[random_int(0, count($cases) - 1)];
    }

    /**
     * @param class-string<UnitEnum> $enum
     * @param callable(UnitEnum): string|null $labelFn
     * @return array<int|string, string>
     */
    public static function toChoices(string $enum, ?callable $labelFn = null): array
    {
        self::assertEnum($enum);
        $result = [];
        foreach ($enum::cases() as $case) {
            $key = $case instanceof BackedEnum ? $case->value : $case->name;
            $label = $labelFn !== null ? $labelFn($case) : $case->name;
            $result[$key] = $label;
        }

        return $result;
    }

    private static function assertEnum(string $enum): void
    {
        if (!enum_exists($enum)) {
            throw new HelperException(sprintf('"%s" is not an enum.', $enum));
        }
    }

    private static function assertBackedEnum(string $enum): void
    {
        self::assertEnum($enum);
        if (!is_subclass_of($enum, BackedEnum::class)) {
            throw new HelperException(sprintf('"%s" is not a backed enum.', $enum));
        }
    }
}
