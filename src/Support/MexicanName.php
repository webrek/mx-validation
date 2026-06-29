<?php

namespace Webrek\MxValidation\Support;

/**
 * Name-parsing rules shared by RFC and CURP generation: accent/Ñ normalization,
 * particle removal, the María/José skip, the inconvenient-word filter, and the
 * letter extraction the SAT/RENAPO algorithms rely on.
 */
final class MexicanName
{
    /** Connector words dropped from a surname before its letters are read. */
    public const PARTICLES = ['DE', 'DEL', 'LA', 'LAS', 'LOS', 'Y', 'MC', 'MAC', 'VON', 'VAN', 'MI'];

    /** First given names skipped in favour of the second (when one exists). */
    public const COMMON = ['MARIA', 'MA', 'MA.', 'JOSE', 'J', 'J.'];

    /** Four-letter combinations replaced (last letter for RFC, second for CURP). */
    public const FORBIDDEN = [
        'BUEI', 'BUEY', 'CACA', 'CACO', 'CAGA', 'CAGO', 'CAKA', 'CAKO', 'COGE', 'COGI',
        'COJA', 'COJE', 'COJI', 'COJO', 'COLA', 'CULO', 'FALO', 'FETO', 'GETA', 'GUEI',
        'GUEY', 'JETA', 'JOTO', 'KACA', 'KACO', 'KAGA', 'KAGO', 'KAKA', 'KAKO', 'KOGE',
        'KOGI', 'KOJA', 'KOJE', 'KOJI', 'KOJO', 'KOLA', 'KULO', 'LILO', 'LOCA', 'LOCO',
        'LOKA', 'LOKO', 'MAME', 'MAMO', 'MEAR', 'MEAS', 'MEON', 'MIAR', 'MION', 'MOCO',
        'MOKO', 'MULA', 'MULO', 'NACA', 'NACO', 'PEDA', 'PEDO', 'PENE', 'PIPI', 'PITO',
        'POPO', 'PUTA', 'PUTO', 'QULO', 'RATA', 'ROBA', 'ROBE', 'ROBO', 'RUIN', 'SENO',
        'TETA', 'VACA', 'VAGA', 'VAGO', 'VAKA', 'VUEI', 'VUEY', 'WUEI', 'WUEY',
    ];

    private const VOWELS = ['A', 'E', 'I', 'O', 'U'];

    /**
     * Upper-case, strip accents (keeping Ñ), and drop anything but letters and
     * single spaces.
     */
    public static function normalize(string $text): string
    {
        $text = mb_strtoupper(trim($text));

        $text = strtr($text, [
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U',
        ]);

        $text = preg_replace('/[^A-ZÑ ]/u', '', $text) ?? '';

        return trim(preg_replace('/\s+/', ' ', $text) ?? '');
    }

    /**
     * The significant word of a surname: normalized, with leading particles
     * removed. Empty string when the surname is absent.
     */
    public static function effectiveSurname(string $surname): string
    {
        $tokens = array_values(array_filter(
            explode(' ', self::normalize($surname)),
            fn (string $token): bool => $token !== '' && ! in_array($token, self::PARTICLES, true),
        ));

        return $tokens[0] ?? '';
    }

    /**
     * The given name used for initials: the first, unless it is a common name
     * (María/José/…) and a second one exists.
     */
    public static function firstGivenName(string $names): string
    {
        $tokens = array_values(array_filter(
            explode(' ', self::normalize($names)),
            fn (string $token): bool => $token !== '',
        ));

        if ($tokens === []) {
            return '';
        }

        if (in_array($tokens[0], self::COMMON, true) && isset($tokens[1])) {
            return $tokens[1];
        }

        return $tokens[0];
    }

    public static function firstInternalVowel(string $word): string
    {
        $length = mb_strlen($word);

        for ($i = 1; $i < $length; $i++) {
            $char = mb_substr($word, $i, 1);

            if (in_array($char, self::VOWELS, true)) {
                return $char;
            }
        }

        return 'X';
    }

    public static function firstInternalConsonant(string $word): string
    {
        $length = mb_strlen($word);

        for ($i = 1; $i < $length; $i++) {
            $char = mb_substr($word, $i, 1);

            if (! in_array($char, self::VOWELS, true)) {
                return $char === 'Ñ' ? 'X' : $char;
            }
        }

        return 'X';
    }

    public static function isForbidden(string $fourLetters): bool
    {
        return in_array($fourLetters, self::FORBIDDEN, true);
    }
}
