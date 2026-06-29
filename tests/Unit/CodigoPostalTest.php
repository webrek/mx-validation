<?php

namespace Webrek\MxValidation\Tests\Unit;

use Webrek\MxValidation\Tests\TestCase;
use Webrek\MxValidation\ValueObjects\CodigoPostal;

class CodigoPostalTest extends TestCase
{
    public function test_it_accepts_well_formed_postal_codes(): void
    {
        $this->assertTrue(CodigoPostal::isValid('01000'));
        $this->assertTrue(CodigoPostal::isValid('64000'));
        $this->assertTrue(CodigoPostal::isValid('99999'));
    }

    public function test_it_rejects_invalid_postal_codes(): void
    {
        $this->assertFalse(CodigoPostal::isValid('00999'));   // 00 prefix
        $this->assertFalse(CodigoPostal::isValid('1234'));    // too short
        $this->assertFalse(CodigoPostal::isValid('123456'));  // too long
        $this->assertFalse(CodigoPostal::isValid('6400A'));   // not numeric
        $this->assertFalse(CodigoPostal::isValid(''));
    }
}
