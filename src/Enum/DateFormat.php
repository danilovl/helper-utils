<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Enum;

enum DateFormat: string
{
    case ISO_8601 = 'Y-m-d\TH:i:sO';
    case ISO_DATE = 'Y-m-d';
    case ISO_TIME = 'H:i:s';
    case ISO_DATETIME = 'Y-m-d H:i:s';
    case RFC_2822 = 'D, d M Y H:i:s O';
    case RFC_3339 = 'Y-m-d\TH:i:sP';
    case HUMAN_DATE = 'F j, Y';
    case HUMAN_DATETIME = 'F j, Y, g:i a';
    case SHORT_DATE = 'd/m/Y';
    case US_DATE = 'm/d/Y';
}
