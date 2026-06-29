<?php

namespace Webrek\MxValidation\Tests\Unit;

use Webrek\MxValidation\Exceptions\InvalidIdentifierException;
use Webrek\MxValidation\Tests\TestCase;
use Webrek\MxValidation\ValueObjects\Clabe;

class ClabeTest extends TestCase
{
    public function test_the_check_digit_matches_the_standard_algorithm(): void
    {
        // First 17 digits 00201007777777777 → control digit 1.
        $this->assertSame(1, Clabe::checkDigit('00201007777777777'));
        $this->assertTrue(Clabe::isValid('002010077777777771'));

        // A sum that is a multiple of ten yields control digit 0 (exercises the
        // final modulo).
        $this->assertSame(0, Clabe::checkDigit('00000000000000000'));
        $this->assertTrue(Clabe::isValid('000000000000000000'));
    }

    public function test_parse_throws_on_invalid(): void
    {
        $this->expectException(InvalidIdentifierException::class);

        Clabe::parse('not-a-clabe');
    }

    public function test_generated_clabes_round_trip(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue(Clabe::isValid($this->faker()->clabe()));
        }
    }

    public function test_it_exposes_bank_branch_and_account(): void
    {
        $clabe = Clabe::parse('002010077777777771');

        $this->assertSame('002', $clabe->bankCode());
        $this->assertSame('Banamex', $clabe->bankName());
        $this->assertSame('010', $clabe->branchCode());
        $this->assertSame('07777777777', $clabe->account());
    }

    public function test_bank_name_is_null_for_an_unknown_code(): void
    {
        $clabe = Clabe::parse($this->faker()->clabe('999'));

        $this->assertNull($clabe->bankName());
    }

    public function test_it_normalizes_spaces(): void
    {
        $this->assertTrue(Clabe::isValid('0020 1007 7777 7777 71'));
    }

    public function test_it_rejects_a_tampered_check_digit(): void
    {
        $this->assertFalse(Clabe::isValid('002010077777777770'));
    }

    public function test_it_rejects_wrong_length(): void
    {
        $this->assertFalse(Clabe::isValid('0020100777777777'));        // 16
        $this->assertFalse(Clabe::isValid('00201007777777777199'));   // 20
        $this->assertFalse(Clabe::isValid(''));
    }
}
