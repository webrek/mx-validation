<?php

namespace Webrek\MxValidation\ValueObjects;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Stringable;
use Webrek\MxValidation\Exceptions\InvalidIdentifierException;
use Webrek\MxValidation\Support\MexicanName;
use Webrek\MxValidation\Support\States;

/**
 * A Mexican CURP (Clave Única de Registro de Población): 18 characters with an
 * embedded birth date, sex, entity of birth, and a trailing check digit.
 */
final class Curp implements Stringable
{
    private const ALPHABET = '0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';

    private const PATTERN = '/^[A-ZÑ][AEIOUX][A-ZÑ]{2}(\d{2})(\d{2})(\d{2})([HMX])([A-Z]{2})[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/u';

    private function __construct(public readonly string $value) {}

    public static function isValid(string $curp): bool
    {
        return self::tryParse($curp) instanceof self;
    }

    public static function tryParse(string $curp): ?self
    {
        $curp = self::normalize($curp);

        if (preg_match(self::PATTERN, $curp, $m) !== 1) {
            return null;
        }

        if (! self::validMonthDay((int) $m[2], (int) $m[3])) {
            return null;
        }

        if (! States::isValid($m[5])) {
            return null;
        }

        if (self::checkDigit(mb_substr($curp, 0, 17)) !== mb_substr($curp, -1)) {
            return null;
        }

        return new self($curp);
    }

    public static function parse(string $curp): self
    {
        return self::tryParse($curp) ?? throw InvalidIdentifierException::for('CURP', $curp);
    }

    /**
     * Build the CURP from a person's data. Everything but the differentiator
     * (position 17) is deterministic; RENAPO assigns that digit/letter to break
     * ties between homonyms, so the default here — 0 for births before 2000, A
     * from 2000 on — is the common case, not a guarantee.
     *
     * @param  string  $sex  H or M
     * @param  string  $state  a two-letter entity code (e.g. DF, JC, NE)
     */
    public static function fromName(
        string $paternalSurname,
        string $maternalSurname,
        string $givenNames,
        DateTimeInterface|string $birthDate,
        string $sex,
        string $state,
    ): self {
        $date = $birthDate instanceof DateTimeInterface ? CarbonImmutable::instance($birthDate) : CarbonImmutable::parse($birthDate);

        $paternal = MexicanName::effectiveSurname($paternalSurname);
        $maternal = MexicanName::effectiveSurname($maternalSurname);
        $name = MexicanName::firstGivenName($givenNames);

        $letters = mb_substr($paternal, 0, 1)
            . MexicanName::firstInternalVowel($paternal)
            . ($maternal !== '' ? mb_substr($maternal, 0, 1) : 'X')
            . mb_substr($name, 0, 1);

        if (MexicanName::isForbidden($letters)) {
            $letters = mb_substr($letters, 0, 1) . 'X' . mb_substr($letters, 2);
        }

        $consonants = MexicanName::firstInternalConsonant($paternal)
            . ($maternal !== '' ? MexicanName::firstInternalConsonant($maternal) : 'X')
            . MexicanName::firstInternalConsonant($name);

        $differentiator = (int) $date->format('Y') < 2000 ? '0' : 'A';

        $first17 = $letters
            . $date->format('ymd')
            . mb_strtoupper($sex)
            . mb_strtoupper($state)
            . $consonants
            . $differentiator;

        return self::parse($first17 . self::checkDigit($first17));
    }

    /**
     * The check digit over the first 17 characters: each value weighted 18 down
     * to 2, then ten minus the sum modulo ten.
     */
    public static function checkDigit(string $first17): string
    {
        $alphabet = mb_str_split(self::ALPHABET);
        $chars = mb_str_split(mb_strtoupper($first17));

        $sum = 0;
        foreach ($chars as $i => $char) {
            $value = array_search($char, $alphabet, true);
            $sum += ($value === false ? 0 : $value) * (18 - $i);
        }

        $digit = 10 - ($sum % 10);

        return (string) ($digit === 10 ? 0 : $digit);
    }

    public function sex(): string
    {
        return mb_substr($this->value, 10, 1);
    }

    public function stateCode(): string
    {
        return mb_substr($this->value, 11, 2);
    }

    public function stateName(): ?string
    {
        return States::name($this->stateCode());
    }

    public function isForeignBorn(): bool
    {
        return $this->stateCode() === 'NE';
    }

    public function birthDate(): CarbonImmutable
    {
        $year = (int) mb_substr($this->value, 4, 2);
        $month = (int) mb_substr($this->value, 6, 2);
        $day = (int) mb_substr($this->value, 8, 2);

        // The homoclave is a digit for births before 2000, a letter from 2000 on.
        $century = ctype_digit(mb_substr($this->value, 16, 1)) ? 1900 : 2000;

        return CarbonImmutable::create($century + $year, $month, $day, 0, 0, 0);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $curp): string
    {
        return preg_replace('/[^0-9A-ZÑ]/u', '', mb_strtoupper(trim($curp))) ?? '';
    }

    private static function validMonthDay(int $month, int $day): bool
    {
        return $month >= 1 && $month <= 12 && $day >= 1 && $day <= 31;
    }
}
