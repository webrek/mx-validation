<?php

namespace Webrek\MxValidation\Tests\Unit;

use Webrek\MxValidation\Tests\TestCase;
use Webrek\MxValidation\ValueObjects\Curp;

class CurpGenerationTest extends TestCase
{
    public function test_it_builds_a_valid_curp_from_a_person(): void
    {
        $curp = Curp::fromName('Pérez', 'López', 'Juan', '1990-05-15', 'H', 'JC');

        $this->assertSame('PELJ900515HJCRPN03', (string) $curp);
        $this->assertSame('H', $curp->sex());
        $this->assertSame('JC', $curp->stateCode());
        $this->assertSame('1990-05-15', $curp->birthDate()->format('Y-m-d'));
    }

    public function test_it_handles_enye_in_a_surname(): void
    {
        $curp = Curp::fromName('Ñañez', 'Soto', 'Ana', '1990-01-01', 'M', 'JC');

        $this->assertSame('ÑASA900101MJCXTN08', (string) $curp);
        $this->assertTrue(Curp::isValid((string) $curp));
    }

    public function test_births_from_2000_use_a_letter_differentiator(): void
    {
        $curp = Curp::fromName('Pérez', 'López', 'Ana', '2005-03-10', 'M', 'JC');

        $this->assertSame('A', mb_substr((string) $curp, 16, 1));
        $this->assertSame('2005-03-10', $curp->birthDate()->format('Y-m-d'));
        $this->assertTrue(Curp::isValid((string) $curp));
    }

    public function test_it_replaces_an_inconvenient_four_letter_combination(): void
    {
        // "CULO" becomes "CXLO" — for CURP the second letter is replaced.
        $curp = Curp::fromName('Cuesta', 'López', 'Óscar', '1990-01-01', 'H', 'DF');

        $this->assertStringStartsWith('CXLO', (string) $curp);
    }

    public function test_it_handles_a_missing_maternal_surname(): void
    {
        // The maternal-surname letter (position 3) and its internal consonant fall back to X.
        $curp = Curp::fromName('García', '', 'Juan', '1990-01-01', 'H', 'JC');

        $this->assertSame('X', mb_substr((string) $curp, 2, 1));
        $this->assertTrue(Curp::isValid((string) $curp));
    }
}
