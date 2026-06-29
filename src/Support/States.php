<?php

namespace Webrek\MxValidation\Support;

/**
 * The two-letter entity codes used inside a CURP. "NE" means born abroad
 * (nacido en el extranjero).
 */
final class States
{
    public const CODES = [
        'AS' => 'Aguascalientes',
        'BC' => 'Baja California',
        'BS' => 'Baja California Sur',
        'CC' => 'Campeche',
        'CL' => 'Coahuila',
        'CM' => 'Colima',
        'CS' => 'Chiapas',
        'CH' => 'Chihuahua',
        'DF' => 'Ciudad de México',
        'DG' => 'Durango',
        'GT' => 'Guanajuato',
        'GR' => 'Guerrero',
        'HG' => 'Hidalgo',
        'JC' => 'Jalisco',
        'MC' => 'México',
        'MN' => 'Michoacán',
        'MS' => 'Morelos',
        'NT' => 'Nayarit',
        'NL' => 'Nuevo León',
        'OC' => 'Oaxaca',
        'PL' => 'Puebla',
        'QT' => 'Querétaro',
        'QR' => 'Quintana Roo',
        'SP' => 'San Luis Potosí',
        'SL' => 'Sinaloa',
        'SR' => 'Sonora',
        'TC' => 'Tabasco',
        'TS' => 'Tamaulipas',
        'TL' => 'Tlaxcala',
        'VZ' => 'Veracruz',
        'YN' => 'Yucatán',
        'ZS' => 'Zacatecas',
        'NE' => 'Nacido en el extranjero',
    ];

    public static function isValid(string $code): bool
    {
        return isset(self::CODES[$code]);
    }

    public static function name(string $code): ?string
    {
        return self::CODES[$code] ?? null;
    }
}
