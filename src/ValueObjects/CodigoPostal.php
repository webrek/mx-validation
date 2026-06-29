<?php

namespace Webrek\MxValidation\ValueObjects;

use Stringable;
use Webrek\MxValidation\Exceptions\InvalidIdentifierException;

/**
 * A Mexican postal code: five digits whose two-digit prefix is a real entity
 * range (01–99). This is a format check — exhaustive validation needs the
 * SEPOMEX catalogue, which this package does not bundle.
 */
final class CodigoPostal implements Stringable
{
    private function __construct(public readonly string $value) {}

    public static function isValid(string $cp): bool
    {
        return preg_match('/^\d{5}$/', $cp) === 1 && substr($cp, 0, 2) !== '00';
    }

    public static function tryParse(string $cp): ?self
    {
        $cp = trim($cp);

        return self::isValid($cp) ? new self($cp) : null;
    }

    public static function parse(string $cp): self
    {
        return self::tryParse($cp) ?? throw InvalidIdentifierException::for('código postal', $cp);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
