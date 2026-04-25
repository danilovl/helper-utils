<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Enum;

enum ByteUnit: string
{
    case B = 'B';
    case KB = 'KB';
    case MB = 'MB';
    case GB = 'GB';
    case TB = 'TB';
    case PB = 'PB';

    public function multiplier(): int
    {
        return match ($this) {
            self::B => 1,
            self::KB => 1_024,
            self::MB => 1_024 ** 2,
            self::GB => 1_024 ** 3,
            self::TB => 1_024 ** 4,
            self::PB => 1_024 ** 5,
        };
    }
}
