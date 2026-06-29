<?php

namespace Webrek\MxValidation\ValueObjects;

use Stringable;
use Webrek\MxValidation\Exceptions\InvalidIdentifierException;
use Webrek\MxValidation\Support\Banks;

/**
 * A CLABE (Clave Bancaria Estandarizada): 18 digits — a 3-digit bank code, a
 * 3-digit branch, an 11-digit account and a weighted mod-10 check digit.
 */
final class Clabe implements Stringable
{
    private const WEIGHTS = [3, 7, 1];

    private function __construct(public readonly string $value) {}

    public static function isValid(string $clabe): bool
    {
        return self::tryParse($clabe) instanceof self;
    }

    public static function tryParse(string $clabe): ?self
    {
        $clabe = self::normalize($clabe);

        if (preg_match('/^\d{18}$/', $clabe) !== 1) {
            return null;
        }

        if (self::checkDigit(substr($clabe, 0, 17)) !== (int) $clabe[17]) {
            return null;
        }

        return new self($clabe);
    }

    public static function parse(string $clabe): self
    {
        return self::tryParse($clabe) ?? throw InvalidIdentifierException::for('CLABE', $clabe);
    }

    /**
     * The control digit for the first 17 digits: each digit times the repeating
     * 3-7-1 weight (kept modulo 10), summed, then ten minus that sum modulo ten.
     */
    public static function checkDigit(string $first17): int
    {
        $sum = 0;
        foreach (str_split($first17) as $i => $digit) {
            $sum += ((int) $digit * self::WEIGHTS[$i % 3]) % 10;
        }

        return (10 - ($sum % 10)) % 10;
    }

    public function bankCode(): string
    {
        return substr($this->value, 0, 3);
    }

    public function bankName(): ?string
    {
        return Banks::name($this->bankCode());
    }

    public function branchCode(): string
    {
        return substr($this->value, 3, 3);
    }

    public function account(): string
    {
        return substr($this->value, 6, 11);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $clabe): string
    {
        return preg_replace('/\D/', '', $clabe) ?? '';
    }
}
