<?php

namespace Webrek\MxValidation\Tests\Unit;

use Webrek\MxValidation\Exceptions\InvalidIdentifierException;
use Webrek\MxValidation\Tests\TestCase;
use Webrek\MxValidation\ValueObjects\Rfc;

class RfcTest extends TestCase
{
    public function test_the_check_digit_matches_the_sat_algorithm(): void
    {
        // Ground truth: GODE561231GR8 is a documented valid RFC.
        $this->assertSame('8', Rfc::checkDigit('GODE561231GR'));
        $this->assertTrue(Rfc::isValid('GODE561231GR8'));
        $this->assertFalse(Rfc::isValid('GODE561231GR9'));
    }

    public function test_the_official_generic_rfcs_are_valid(): void
    {
        $this->assertTrue(Rfc::isValid('XAXX010101000'));
        $this->assertTrue(Rfc::isValid('XEXX010101000'));
        $this->assertTrue(Rfc::parse('XAXX010101000')->isGeneric());
    }

    public function test_generated_rfcs_round_trip(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue(Rfc::isValid($this->faker()->rfc()), 'persona física');
            $this->assertTrue(Rfc::isValid($this->faker()->rfc(moral: true)), 'persona moral');
        }
    }

    public function test_it_normalizes_case_and_punctuation(): void
    {
        $rfc = Rfc::parse('  xaxx-010101-000 ');

        $this->assertSame('XAXX010101000', $rfc->value);
        $this->assertSame('XAXX010101000', (string) $rfc);
    }

    public function test_it_distinguishes_personas_fisicas_and_morales(): void
    {
        $fisica = Rfc::parse('XAXX010101000');
        $this->assertTrue($fisica->isFisica());
        $this->assertFalse($fisica->isMoral());
        $this->assertSame('fisica', $fisica->type());

        $moral = Rfc::parse($this->faker()->rfc(moral: true));
        $this->assertTrue($moral->isMoral());
        $this->assertSame('moral', $moral->type());
    }

    public function test_it_rejects_a_tampered_check_digit(): void
    {
        // Flip the final check character of an otherwise valid generic RFC.
        $this->assertFalse(Rfc::isValid('XAXX010101001'));
    }

    public function test_it_rejects_malformed_values(): void
    {
        $this->assertFalse(Rfc::isValid(''));
        $this->assertFalse(Rfc::isValid('ABC'));
        $this->assertFalse(Rfc::isValid('XAXX011301000'));   // month 13
        $this->assertFalse(Rfc::isValid('XAXX010132000'));   // day 32
        $this->assertFalse(Rfc::isValid('XAXX0101010000'));  // too long
    }

    public function test_parse_throws_on_invalid(): void
    {
        $this->expectException(InvalidIdentifierException::class);

        Rfc::parse('not-an-rfc');
    }
}
