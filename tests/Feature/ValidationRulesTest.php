<?php

namespace Webrek\MxValidation\Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Webrek\MxValidation\Laravel\Rules;
use Webrek\MxValidation\Tests\LaravelTestCase;

class ValidationRulesTest extends LaravelTestCase
{
    public function test_string_rules_are_registered(): void
    {
        $data = [
            'rfc' => 'XAXX010101000',
            'curp' => $this->faker()->curp(),
            'clabe' => '002010077777777771',
            'nss' => $this->faker()->nss(),
            'codigo_postal' => '64000',
        ];

        $validator = Validator::make($data, [
            'rfc' => 'required|rfc',
            'curp' => 'required|curp',
            'clabe' => 'required|clabe',
            'nss' => 'required|nss',
            'codigo_postal' => 'required|codigo_postal',
        ]);

        $this->assertTrue($validator->passes());
    }

    public function test_string_rules_reject_bad_values_with_spanish_messages(): void
    {
        $validator = Validator::make(
            ['rfc' => 'nope', 'curp' => 'nope', 'clabe' => '123', 'nss' => '123', 'codigo_postal' => '00000'],
            [
                'rfc' => 'rfc',
                'curp' => 'curp',
                'clabe' => 'clabe',
                'nss' => 'nss',
                'codigo_postal' => 'codigo_postal',
            ],
        );

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('RFC', $validator->errors()->first('rfc'));
        $this->assertStringContainsString('CURP', $validator->errors()->first('curp'));
        $this->assertStringContainsString('CLABE', $validator->errors()->first('clabe'));
    }

    public function test_object_rules_work(): void
    {
        $valid = Validator::make(['rfc' => 'XAXX010101000'], ['rfc' => [new Rules\Rfc]]);
        $this->assertTrue($valid->passes());

        $invalid = Validator::make(['rfc' => 'bad'], ['rfc' => [new Rules\Rfc]]);
        $this->assertTrue($invalid->fails());
    }
}
