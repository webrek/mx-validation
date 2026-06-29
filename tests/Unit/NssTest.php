<?php

namespace Webrek\MxValidation\Tests\Unit;

use Webrek\MxValidation\Tests\TestCase;
use Webrek\MxValidation\ValueObjects\Nss;

class NssTest extends TestCase
{
    public function test_the_check_digit_matches_the_luhn_algorithm(): void
    {
        $this->assertSame(3, Nss::checkDigit('1234567890'));
        $this->assertTrue(Nss::isValid('12345678903'));
        $this->assertFalse(Nss::isValid('12345678904'));
    }

    public function test_generated_nss_round_trip(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue(Nss::isValid($this->faker()->nss()));
        }
    }

    public function test_it_exposes_the_subdelegation_code(): void
    {
        $first10 = '12' . substr($this->faker()->nss(), 2, 8);
        $nss = Nss::parse($first10 . Nss::checkDigit($first10));

        $this->assertSame('12', $nss->subdelegationCode());
    }

    public function test_it_rejects_a_tampered_check_digit(): void
    {
        $valid = $this->faker()->nss();
        $last = (int) substr($valid, 10);
        $tampered = substr($valid, 0, 10) . ($last === 9 ? '0' : '9');

        $this->assertFalse(Nss::isValid($tampered));
    }

    public function test_it_rejects_wrong_length(): void
    {
        $this->assertFalse(Nss::isValid('1234567890'));     // 10
        $this->assertFalse(Nss::isValid('123456789012'));   // 12
        $this->assertFalse(Nss::isValid('abcdefghijk'));
        $this->assertFalse(Nss::isValid(''));
    }
}
