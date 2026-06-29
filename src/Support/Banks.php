<?php

namespace Webrek\MxValidation\Support;

/**
 * The three-digit institution codes that open a CLABE, per the CNBV/Banxico
 * catalogue. Not exhaustive — covers the institutions most apps encounter.
 */
final class Banks
{
    public const NAMES = [
        '002' => 'Banamex',
        '006' => 'Banobras',
        '009' => 'Banobras',
        '012' => 'BBVA México',
        '014' => 'Santander',
        '019' => 'Banjército',
        '021' => 'HSBC',
        '030' => 'Banco del Bajío',
        '036' => 'Inbursa',
        '042' => 'Mifel',
        '044' => 'Scotiabank',
        '058' => 'Banregio',
        '059' => 'Invex',
        '060' => 'Bansi',
        '062' => 'Afirme',
        '072' => 'Banorte',
        '106' => 'Bank of America',
        '108' => 'MUFG',
        '110' => 'JP Morgan',
        '127' => 'Azteca',
        '128' => 'Autofin',
        '129' => 'Barclays',
        '130' => 'Compartamos',
        '137' => 'BanCoppel',
        '140' => 'CrediBanco',
        '143' => 'CIBanco',
        '147' => 'Bankaool',
        '148' => 'PagaTodo',
        '150' => 'Inmobiliario',
        '151' => 'Donde',
        '152' => 'Bancrea',
        '155' => 'ICBC',
        '156' => 'Sabadell',
        '166' => 'Banco del Bienestar',
        '168' => 'Hipotecaria Federal',
        '600' => 'Monex',
        '601' => 'GBM',
        '602' => 'Masari',
        '605' => 'Valué',
        '608' => 'Vector',
        '616' => 'Finamex',
        '634' => 'Fincomún',
        '646' => 'STP',
        '652' => 'Crédito Maestro',
        '659' => 'Asp Integra Opc',
        '670' => 'Libertad',
        '706' => 'Arcus',
        '710' => 'NVIO',
        '722' => 'Mercado Pago',
    ];

    public static function name(string $code): ?string
    {
        return self::NAMES[$code] ?? null;
    }
}
