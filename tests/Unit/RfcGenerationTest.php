<?php

namespace Webrek\MxValidation\Tests\Unit;

use DateTimeImmutable;
use Webrek\MxValidation\Tests\TestCase;
use Webrek\MxValidation\ValueObjects\Rfc;

class RfcGenerationTest extends TestCase
{
    public function test_it_reproduces_the_official_sat_example(): void
    {
        // The SAT's worked example: Emma Gómez Díaz, born 1956-12-31.
        $this->assertSame('GODE561231GR8', (string) Rfc::fromName('Gómez', 'Díaz', 'Emma', '1956-12-31'));
    }

    public function test_it_builds_a_valid_rfc_from_a_name(): void
    {
        $rfc = Rfc::fromName('Pérez', 'López', 'Juan', '1990-05-15');

        $this->assertSame('PELJ9005152A0', (string) $rfc);
        $this->assertTrue($rfc->isFisica());
    }

    public function test_it_accepts_a_date_object(): void
    {
        $rfc = Rfc::fromName('Gómez', 'Díaz', 'Emma', new DateTimeImmutable('1956-12-31'));

        $this->assertSame('GODE561231GR8', (string) $rfc);
    }

    public function test_it_skips_a_common_first_name(): void
    {
        // "José" is skipped, so the fourth letter comes from "Luis".
        $rfc = Rfc::fromName('Pérez', 'López', 'José Luis', '1990-05-05');

        $this->assertStringStartsWith('PELL900505', (string) $rfc);
    }

    public function test_it_handles_a_missing_maternal_surname(): void
    {
        // No maternal surname: the given name supplies the third and fourth letters.
        $rfc = Rfc::fromName('García', '', 'Juan', '1990-01-01');

        $this->assertStringStartsWith('GAJU900101', (string) $rfc);
    }

    public function test_it_replaces_an_inconvenient_four_letter_combination(): void
    {
        // Cuesta López Óscar would spell "CULO"; the last letter becomes X.
        $rfc = Rfc::fromName('Cuesta', 'López', 'Óscar', '1990-01-01');

        $this->assertStringStartsWith('CULX900101', (string) $rfc);
    }

    public function test_it_handles_enye_in_a_surname(): void
    {
        // Multibyte name: needs mb_* handling for the initials.
        $rfc = Rfc::fromName('Ñañez', 'Soto', 'Ana', '1990-01-01');

        $this->assertSame('ÑASA900101KU8', (string) $rfc);
        $this->assertTrue(Rfc::isValid((string) $rfc));
    }

    public function test_generated_rfcs_are_always_valid(): void
    {
        foreach (['Torres' => 'Vega', 'Gómez' => 'Díaz', 'Ramírez' => 'Sánchez'] as $p => $m) {
            $rfc = Rfc::fromName($p, $m, 'Ana', '1991-07-08');
            $this->assertTrue(Rfc::isValid((string) $rfc));
        }
    }
}
