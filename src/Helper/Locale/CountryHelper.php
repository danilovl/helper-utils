<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Locale;

use Locale;
use Symfony\Component\Intl\Countries;
use Throwable;

final class CountryHelper
{
    /** Subset of common alpha2 → alpha3 mapping. Used as fallback when symfony/intl is not installed. */
    private const array ALPHA2_TO_ALPHA3 = [
        'AD' => 'AND', 'AE' => 'ARE', 'AF' => 'AFG', 'AG' => 'ATG', 'AI' => 'AIA', 'AL' => 'ALB',
        'AM' => 'ARM', 'AO' => 'AGO', 'AR' => 'ARG', 'AS' => 'ASM', 'AT' => 'AUT', 'AU' => 'AUS',
        'AW' => 'ABW', 'AX' => 'ALA', 'AZ' => 'AZE', 'BA' => 'BIH', 'BB' => 'BRB', 'BD' => 'BGD',
        'BE' => 'BEL', 'BF' => 'BFA', 'BG' => 'BGR', 'BH' => 'BHR', 'BI' => 'BDI', 'BJ' => 'BEN',
        'BL' => 'BLM', 'BM' => 'BMU', 'BN' => 'BRN', 'BO' => 'BOL', 'BR' => 'BRA', 'BS' => 'BHS',
        'BT' => 'BTN', 'BW' => 'BWA', 'BY' => 'BLR', 'BZ' => 'BLZ', 'CA' => 'CAN', 'CC' => 'CCK',
        'CD' => 'COD', 'CF' => 'CAF', 'CG' => 'COG', 'CH' => 'CHE', 'CI' => 'CIV', 'CK' => 'COK',
        'CL' => 'CHL', 'CM' => 'CMR', 'CN' => 'CHN', 'CO' => 'COL', 'CR' => 'CRI', 'CU' => 'CUB',
        'CV' => 'CPV', 'CW' => 'CUW', 'CY' => 'CYP', 'CZ' => 'CZE', 'DE' => 'DEU', 'DJ' => 'DJI',
        'DK' => 'DNK', 'DM' => 'DMA', 'DO' => 'DOM', 'DZ' => 'DZA', 'EC' => 'ECU', 'EE' => 'EST',
        'EG' => 'EGY', 'ER' => 'ERI', 'ES' => 'ESP', 'ET' => 'ETH', 'FI' => 'FIN', 'FJ' => 'FJI',
        'FK' => 'FLK', 'FM' => 'FSM', 'FO' => 'FRO', 'FR' => 'FRA', 'GA' => 'GAB', 'GB' => 'GBR',
        'GD' => 'GRD', 'GE' => 'GEO', 'GH' => 'GHA', 'GI' => 'GIB', 'GL' => 'GRL', 'GM' => 'GMB',
        'GN' => 'GIN', 'GR' => 'GRC', 'GT' => 'GTM', 'GU' => 'GUM', 'GW' => 'GNB', 'GY' => 'GUY',
        'HK' => 'HKG', 'HN' => 'HND', 'HR' => 'HRV', 'HT' => 'HTI', 'HU' => 'HUN', 'ID' => 'IDN',
        'IE' => 'IRL', 'IL' => 'ISR', 'IM' => 'IMN', 'IN' => 'IND', 'IQ' => 'IRQ', 'IR' => 'IRN',
        'IS' => 'ISL', 'IT' => 'ITA', 'JE' => 'JEY', 'JM' => 'JAM', 'JO' => 'JOR', 'JP' => 'JPN',
        'KE' => 'KEN', 'KG' => 'KGZ', 'KH' => 'KHM', 'KI' => 'KIR', 'KM' => 'COM', 'KN' => 'KNA',
        'KP' => 'PRK', 'KR' => 'KOR', 'KW' => 'KWT', 'KY' => 'CYM', 'KZ' => 'KAZ', 'LA' => 'LAO',
        'LB' => 'LBN', 'LC' => 'LCA', 'LI' => 'LIE', 'LK' => 'LKA', 'LR' => 'LBR', 'LS' => 'LSO',
        'LT' => 'LTU', 'LU' => 'LUX', 'LV' => 'LVA', 'LY' => 'LBY', 'MA' => 'MAR', 'MC' => 'MCO',
        'MD' => 'MDA', 'ME' => 'MNE', 'MG' => 'MDG', 'MH' => 'MHL', 'MK' => 'MKD', 'ML' => 'MLI',
        'MM' => 'MMR', 'MN' => 'MNG', 'MO' => 'MAC', 'MR' => 'MRT', 'MT' => 'MLT', 'MU' => 'MUS',
        'MV' => 'MDV', 'MW' => 'MWI', 'MX' => 'MEX', 'MY' => 'MYS', 'MZ' => 'MOZ', 'NA' => 'NAM',
        'NE' => 'NER', 'NG' => 'NGA', 'NI' => 'NIC', 'NL' => 'NLD', 'NO' => 'NOR', 'NP' => 'NPL',
        'NR' => 'NRU', 'NU' => 'NIU', 'NZ' => 'NZL', 'OM' => 'OMN', 'PA' => 'PAN', 'PE' => 'PER',
        'PG' => 'PNG', 'PH' => 'PHL', 'PK' => 'PAK', 'PL' => 'POL', 'PR' => 'PRI', 'PS' => 'PSE',
        'PT' => 'PRT', 'PW' => 'PLW', 'PY' => 'PRY', 'QA' => 'QAT', 'RO' => 'ROU', 'RS' => 'SRB',
        'RU' => 'RUS', 'RW' => 'RWA', 'SA' => 'SAU', 'SB' => 'SLB', 'SC' => 'SYC', 'SD' => 'SDN',
        'SE' => 'SWE', 'SG' => 'SGP', 'SI' => 'SVN', 'SK' => 'SVK', 'SL' => 'SLE', 'SM' => 'SMR',
        'SN' => 'SEN', 'SO' => 'SOM', 'SR' => 'SUR', 'SS' => 'SSD', 'ST' => 'STP', 'SV' => 'SLV',
        'SY' => 'SYR', 'SZ' => 'SWZ', 'TC' => 'TCA', 'TD' => 'TCD', 'TG' => 'TGO', 'TH' => 'THA',
        'TJ' => 'TJK', 'TL' => 'TLS', 'TM' => 'TKM', 'TN' => 'TUN', 'TO' => 'TON', 'TR' => 'TUR',
        'TT' => 'TTO', 'TV' => 'TUV', 'TW' => 'TWN', 'TZ' => 'TZA', 'UA' => 'UKR', 'UG' => 'UGA',
        'US' => 'USA', 'UY' => 'URY', 'UZ' => 'UZB', 'VA' => 'VAT', 'VC' => 'VCT', 'VE' => 'VEN',
        'VG' => 'VGB', 'VI' => 'VIR', 'VN' => 'VNM', 'VU' => 'VUT', 'WS' => 'WSM', 'YE' => 'YEM',
        'ZA' => 'ZAF', 'ZM' => 'ZMB', 'ZW' => 'ZWE',
    ];

    /** Subset of country calling codes. */
    private const array CALLING_CODES = [
        'US' => '+1', 'CA' => '+1', 'GB' => '+44', 'DE' => '+49', 'FR' => '+33', 'IT' => '+39',
        'ES' => '+34', 'NL' => '+31', 'BE' => '+32', 'CH' => '+41', 'AT' => '+43', 'SE' => '+46',
        'NO' => '+47', 'DK' => '+45', 'FI' => '+358', 'PL' => '+48', 'CZ' => '+420', 'SK' => '+421',
        'HU' => '+36', 'RO' => '+40', 'BG' => '+359', 'GR' => '+30', 'PT' => '+351', 'IE' => '+353',
        'RU' => '+7', 'UA' => '+380', 'BY' => '+375', 'KZ' => '+7', 'JP' => '+81', 'CN' => '+86',
        'KR' => '+82', 'IN' => '+91', 'AU' => '+61', 'NZ' => '+64', 'BR' => '+55', 'AR' => '+54',
        'MX' => '+52', 'ZA' => '+27', 'EG' => '+20', 'TR' => '+90', 'SA' => '+966', 'AE' => '+971',
        'IL' => '+972', 'TH' => '+66', 'VN' => '+84', 'ID' => '+62', 'PH' => '+63', 'MY' => '+60',
        'SG' => '+65', 'HK' => '+852', 'TW' => '+886',
    ];

    public static function getName(string $alpha2, string $locale = 'en'): string
    {
        $alpha2 = mb_strtoupper($alpha2);

        if (class_exists(Countries::class)) {
            try {
                return Countries::getName($alpha2, $locale);
            } catch (Throwable) {
                return $alpha2;
            }
        }

        if (class_exists(Locale::class)) {
            $name = Locale::getDisplayRegion('-' . $alpha2, $locale);
            if ($name !== '' && $name !== $alpha2) {
                return $name;
            }
        }

        return $alpha2;
    }

    public static function alpha2ToAlpha3(string $alpha2): ?string
    {
        $alpha2 = mb_strtoupper($alpha2);

        if (class_exists(Countries::class)) {
            try {
                return Countries::getAlpha3Code($alpha2);
            } catch (Throwable) {
                return null;
            }
        }

        return self::ALPHA2_TO_ALPHA3[$alpha2] ?? null;
    }

    public static function alpha3ToAlpha2(string $alpha3): ?string
    {
        $alpha3 = mb_strtoupper($alpha3);

        if (class_exists(Countries::class)) {
            try {
                return Countries::getAlpha2Code($alpha3);
            } catch (Throwable) {
                return null;
            }
        }

        return array_search($alpha3, self::ALPHA2_TO_ALPHA3, true) ?: null;
    }

    public static function getFlagEmoji(string $alpha2): string
    {
        $alpha2 = mb_strtoupper($alpha2);
        if (!preg_match('~^[A-Z]{2}$~', $alpha2)) {
            return '';
        }
        $base = 0x1_F1_E6 - ord('A');

        return mb_chr($base + ord($alpha2[0]), 'UTF-8') . mb_chr($base + ord($alpha2[1]), 'UTF-8');
    }

    public static function getCallingCode(string $alpha2): ?string
    {
        return self::CALLING_CODES[mb_strtoupper($alpha2)] ?? null;
    }

    public static function isValid(string $alpha2): bool
    {
        $alpha2 = mb_strtoupper($alpha2);
        if (class_exists(Countries::class)) {
            return Countries::exists($alpha2);
        }

        return isset(self::ALPHA2_TO_ALPHA3[$alpha2]);
    }

    /**
     * @return list<string>
     */
    public static function getAllAlpha2(): array
    {
        if (class_exists(Countries::class)) {
            return array_values(Countries::getCountryCodes());
        }

        return array_keys(self::ALPHA2_TO_ALPHA3);
    }
}
