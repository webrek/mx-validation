<?php

namespace Webrek\MxValidation\Tests\Unit;

use Webrek\MxValidation\Tests\TestCase;
use Webrek\MxValidation\ValueObjects\Curp;

class CurpTest extends TestCase
{
    /**
     * Build a valid CURP from a known 17-character prefix plus the computed
     * check digit, so accessor assertions have stable inputs.
     */
    private function curp(string $first17): string
    {
        return $first17 . Curp::checkDigit($first17);
    }

    public function test_the_check_digit_matches_the_official_algorithm(): void
    {
        // Ground truth: HEGG560427MVZRRL04 is a documented valid CURP.
        $this->assertSame('4', Curp::checkDigit('HEGG560427MVZRRL0'));
        $this->assertTrue(Curp::isValid('HEGG560427MVZRRL04'));
        $this->assertFalse(Curp::isValid('HEGG560427MVZRRL05'));
    }

    public function test_generated_curps_round_trip(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue(Curp::isValid($this->faker()->curp()));
        }
    }

    public function test_it_exposes_sex_state_and_birth_date(): void
    {
        $curp = Curp::parse('HEGG560427MVZRRL04');

        $this->assertSame('M', $curp->sex());
        $this->assertSame('VZ', $curp->stateCode());
        $this->assertSame('Veracruz', $curp->stateName());
        $this->assertFalse($curp->isForeignBorn());
        $this->assertSame('1956-04-27', $curp->birthDate()->format('Y-m-d'));
    }

    public function test_a_letter_homoclave_places_the_birth_year_in_the_2000s(): void
    {
        // HEGG050427MVZRRLA7 — same shape with a 2005 birth year (letter homoclave).
        $curp = Curp::parse('HEGG050427MVZRRLA7');

        $this->assertSame('2005-04-27', $curp->birthDate()->format('Y-m-d'));
    }

    public function test_it_detects_foreign_born(): void
    {
        $curp = Curp::parse($this->curp('PEPJ900101HNERRN0'));

        $this->assertTrue($curp->isForeignBorn());
        $this->assertSame('Nacido en el extranjero', $curp->stateName());
    }

    public function test_it_rejects_a_tampered_check_digit(): void
    {
        $valid = $this->curp('PEPJ900101HDFRRN0');
        $tampered = substr($valid, 0, 17) . ((int) substr($valid, 17) === 9 ? '0' : '9');

        $this->assertFalse(Curp::isValid($tampered));
    }

    public function test_it_rejects_an_invalid_state_code(): void
    {
        // ZZ is not a real entity code.
        $this->assertFalse(Curp::isValid($this->curp('PEPJ900101HZZRRN0')));
    }

    public function test_it_rejects_malformed_values(): void
    {
        $this->assertFalse(Curp::isValid(''));
        $this->assertFalse(Curp::isValid('PEPJ900101HDFRRN'));     // too short
        $this->assertFalse(Curp::isValid('PEPJ901301HDFRRN09'));   // month 13
    }
}
