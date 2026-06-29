<?php

namespace Webrek\MxValidation\ValueObjects;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Stringable;
use Webrek\MxValidation\Exceptions\InvalidIdentifierException;
use Webrek\MxValidation\Support\MexicanName;

/**
 * A Mexican RFC (Registro Federal de Contribuyentes).
 *
 * Validates structure, the embedded date, and the official check digit
 * (dígito verificador) — the last character of the homoclave. Personas físicas
 * carry 13 characters (4 name letters), personas morales 12 (3 letters).
 */
final class Rfc implements Stringable
{
    /** Order matters: index = the character's value in the check-digit sum. */
    private const ALPHABET = '0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZ Ñ';

    /** The SAT generic RFCs for the general public and foreigners. */
    public const GENERIC = ['XAXX010101000', 'XEXX010101000'];

    private function __construct(
        public readonly string $value,
        private readonly bool $moral,
        private readonly bool $generic,
    ) {}

    public static function isValid(string $rfc): bool
    {
        return self::tryParse($rfc) instanceof self;
    }

    public static function tryParse(string $rfc): ?self
    {
        $rfc = self::normalize($rfc);

        if (preg_match('/^([A-ZÑ&]{3,4})(\d{2})(\d{2})(\d{2})[A-Z\d]{2}[A-Z\d]$/u', $rfc, $m) !== 1) {
            return null;
        }

        $moral = mb_strlen($m[1]) === 3;

        if (! self::validMonthDay((int) $m[3], (int) $m[4])) {
            return null;
        }

        // The SAT generic RFCs are administrative and intentionally do not
        // satisfy the check-digit algorithm, so they are whitelisted.
        if (in_array($rfc, self::GENERIC, true)) {
            return new self($rfc, $moral, true);
        }

        if (self::checkDigit(mb_substr($rfc, 0, -1)) !== mb_substr($rfc, -1)) {
            return null;
        }

        return new self($rfc, $moral, false);
    }

    public static function parse(string $rfc): self
    {
        return self::tryParse($rfc) ?? throw InvalidIdentifierException::for('RFC', $rfc);
    }

    /**
     * Build the RFC of a persona física from their name and birth date, using
     * the SAT initials rules, the name-hash homoclave and the check digit.
     *
     * The homoclave is the presumptive value the SAT algorithm yields; the
     * authority may assign a different one to break ties between people with the
     * same name and birth date, so treat the result as a strong candidate, not
     * a substitute for the Constancia de Situación Fiscal.
     */
    public static function fromName(
        string $paternalSurname,
        string $maternalSurname,
        string $givenNames,
        DateTimeInterface|string $birthDate,
    ): self {
        $date = $birthDate instanceof DateTimeInterface ? CarbonImmutable::instance($birthDate) : CarbonImmutable::parse($birthDate);

        $paternal = MexicanName::effectiveSurname($paternalSurname);
        $maternal = MexicanName::effectiveSurname($maternalSurname);
        $name = MexicanName::firstGivenName($givenNames);

        if ($maternal !== '') {
            $letters = mb_substr($paternal, 0, 1)
                . MexicanName::firstInternalVowel($paternal)
                . mb_substr($maternal, 0, 1)
                . mb_substr($name, 0, 1);
        } else {
            // No maternal surname: the first two letters of the given name fill in.
            $letters = mb_substr($paternal, 0, 1)
                . MexicanName::firstInternalVowel($paternal)
                . mb_substr($name, 0, 2);
        }

        if (MexicanName::isForbidden($letters)) {
            $letters = mb_substr($letters, 0, 3) . 'X';
        }

        $body = $letters
            . $date->format('ymd')
            . self::homoclave(MexicanName::normalize("{$paternalSurname} {$maternalSurname} {$givenNames}"));

        return self::parse($body . self::checkDigit($body));
    }

    /**
     * The two-character homoclave: a hash of the full name. Each character is
     * mapped to two digits, products of adjacent digit pairs are summed, and the
     * sum modulo 1000 indexes a 34-character alphabet twice.
     */
    private static function homoclave(string $fullName): string
    {
        static $map = [
            ' ' => '00', '0' => '00', '1' => '01', '2' => '02', '3' => '03', '4' => '04',
            '5' => '05', '6' => '06', '7' => '07', '8' => '08', '9' => '09', '&' => '10',
            'A' => '11', 'B' => '12', 'C' => '13', 'D' => '14', 'E' => '15', 'F' => '16',
            'G' => '17', 'H' => '18', 'I' => '19', 'J' => '21', 'K' => '22', 'L' => '23',
            'M' => '24', 'N' => '25', 'O' => '26', 'P' => '27', 'Q' => '28', 'R' => '29',
            'S' => '32', 'T' => '33', 'U' => '34', 'V' => '35', 'W' => '36', 'X' => '37',
            'Y' => '38', 'Z' => '39', 'Ñ' => '40',
        ];

        $digits = '0';
        foreach (mb_str_split($fullName) as $char) {
            $digits .= $map[$char] ?? '00';
        }

        $sum = 0;
        $length = strlen($digits);
        for ($i = 0; $i < $length - 1; $i++) {
            $sum += ((int) substr($digits, $i, 2)) * (int) $digits[$i + 1];
        }

        $residue = $sum % 1000;
        $alphabet = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';

        return $alphabet[intdiv($residue, 34)] . $alphabet[$residue % 34];
    }

    /**
     * The official RFC check digit for everything but its last character. The
     * body is left-padded with a space to 12 positions (personas morales),
     * then each character's value is weighted 13 down to 2.
     */
    public static function checkDigit(string $body): string
    {
        $alphabet = mb_str_split(self::ALPHABET);
        $chars = mb_str_split(mb_strtoupper($body));

        while (count($chars) < 12) {
            array_unshift($chars, ' ');
        }

        $sum = 0;
        foreach ($chars as $i => $char) {
            $value = array_search($char, $alphabet, true);
            $sum += ($value === false ? 0 : $value) * (13 - $i);
        }

        $mod = $sum % 11;

        if ($mod === 0) {
            return '0';
        }

        $digit = 11 - $mod;

        return $digit === 10 ? 'A' : (string) $digit;
    }

    public function isFisica(): bool
    {
        return ! $this->moral;
    }

    public function isMoral(): bool
    {
        return $this->moral;
    }

    public function isGeneric(): bool
    {
        return $this->generic;
    }

    public function type(): string
    {
        return $this->moral ? 'moral' : 'fisica';
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $rfc): string
    {
        return preg_replace('/[^0-9A-ZÑ&]/u', '', mb_strtoupper(trim($rfc))) ?? '';
    }

    private static function validMonthDay(int $month, int $day): bool
    {
        return $month >= 1 && $month <= 12 && $day >= 1 && $day <= 31;
    }
}
