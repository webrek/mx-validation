<?php

namespace Webrek\MxValidation\Faker;

use Faker\Provider\Base;
use Webrek\MxValidation\Support\Banks;
use Webrek\MxValidation\Support\States;
use Webrek\MxValidation\ValueObjects\Clabe;
use Webrek\MxValidation\ValueObjects\Curp;
use Webrek\MxValidation\ValueObjects\Nss;
use Webrek\MxValidation\ValueObjects\Rfc;

/**
 * Faker provider generating structurally valid Mexican identifiers (with
 * correct check digits) for seeding and tests. Values are well-formed but
 * fictitious — the name-derived characters do not map to a real person.
 */
class MxProvider extends Base
{
    private const CONSONANTS = 'BCDFGHJKLMNPQRSTVWXYZ';

    public function rfc(bool $moral = false): string
    {
        $body = $this->letters($moral ? 3 : 4) . $this->birthDigits() . strtoupper($this->lexify('??'));

        return $body . Rfc::checkDigit($body);
    }

    public function curp(): string
    {
        $first17 = $this->letters(1)
            . static::randomElement(['A', 'E', 'I', 'O', 'U'])
            . $this->letters(2)
            . $this->birthDigits()
            . static::randomElement(['H', 'M'])
            . static::randomElement(array_keys(States::CODES))
            . $this->consonants(3)
            . (string) static::numberBetween(0, 9);

        return $first17 . Curp::checkDigit($first17);
    }

    public function clabe(?string $bankCode = null): string
    {
        $first17 = ($bankCode ?? static::randomElement(array_keys(Banks::NAMES))) . $this->numerify('##############');

        return $first17 . Clabe::checkDigit($first17);
    }

    public function nss(): string
    {
        $first10 = $this->numerify('##########');

        return $first10 . Nss::checkDigit($first10);
    }

    private function letters(int $count): string
    {
        return strtoupper($this->lexify(str_repeat('?', $count)));
    }

    private function consonants(int $count): string
    {
        $out = '';
        for ($i = 0; $i < $count; $i++) {
            $out .= self::CONSONANTS[static::numberBetween(0, strlen(self::CONSONANTS) - 1)];
        }

        return $out;
    }

    private function birthDigits(): string
    {
        return sprintf(
            '%02d%02d%02d',
            static::numberBetween(0, 99),
            static::numberBetween(1, 12),
            static::numberBetween(1, 28),
        );
    }
}
