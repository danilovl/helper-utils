<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Enum;

enum ComparisonOperator: string
{
    case EQUAL = '==';
    case STRICT_EQUAL = '===';
    case NOT_EQUAL = '!=';
    case STRICT_NOT_EQUAL = '!==';
    case GREATER_THAN = '>';
    case GREATER_OR_EQUAL = '>=';
    case LESS_THAN = '<';
    case LESS_OR_EQUAL = '<=';
    case BETWEEN = 'between';
    case IN = 'in';
    case NOT_IN = 'not_in';
    case CONTAINS = 'contains';
    case STARTS_WITH = 'starts_with';
    case ENDS_WITH = 'ends_with';
    case MATCHES_REGEX = 'regex';
}
