<?php

namespace Webrek\MxValidation\ValueObjects;

use Stringable;
use Webrek\MxValidation\Exceptions\InvalidIdentifierException;

/**
 * An IMSS social-security number (Número de Seguridad Social): 11 digits whose
 * last digit is a Luhn check over the preceding ten.
 */
final class Nss implements Stringable
{
    private function __construct(public readonly string $value) {}

    public static function isValid(string $nss): bool
    {
        return self::tryParse($nss) instanceof self;
    }

    public static function tryParse(string $nss): ?self
    {
        $nss = self::normalize($nss);

        if (preg_match('/^\d{11}$/', $nss) !== 1) {
            return null;
        }

        if (self::checkDigit(substr($nss, 0, 10)) !== (int) $nss[10]) {
            return null;
        }

        return new self($nss);
    }

    public static function parse(string $nss): self
    {
        return self::tryParse($nss) ?? throw InvalidIdentifierException::for('NSS', $nss);
    }

    /**
     * The Luhn check digit for the first ten digits.
     */
    public static function checkDigit(string $first10): int
    {
        $sum = 0;
        $digits = str_split($first10);

        // The rightmost of the ten is doubled, then every second digit moving left.
        foreach (array_reverse($digits) as $i => $digit) {
            $digit = (int) $digit;

            if ($i % 2 === 0) {
                $digit *= 2;

                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
        }

        return (10 - ($sum % 10)) % 10;
    }

    public function subdelegationCode(): string
    {
        return substr($this->value, 0, 2);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $nss): string
    {
        return preg_replace('/\D/', '', $nss) ?? '';
    }
}
