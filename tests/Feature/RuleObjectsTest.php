<?php

namespace Webrek\MxValidation\Tests\Feature;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Webrek\MxValidation\Laravel\Rules;
use Webrek\MxValidation\Tests\LaravelTestCase;

class RuleObjectsTest extends LaravelTestCase
{
    /**
     * @return array<string, array{0: ValidationRule, 1: string, 2: string}>
     */
    public static function rules(): array
    {
        return [
            'rfc' => [new Rules\Rfc, 'GODE561231GR8', 'GODE561231GR9'],
            'curp' => [new Rules\Curp, 'HEGG560427MVZRRL04', 'HEGG560427MVZRRL05'],
            'clabe' => [new Rules\Clabe, '002010077777777771', '002010077777777770'],
            'nss' => [new Rules\Nss, '12345678903', '12345678904'],
            'codigo_postal' => [new Rules\CodigoPostal, '64000', '00000'],
        ];
    }

    #[DataProvider('rules')]
    public function test_rule_objects_accept_valid_and_reject_invalid(ValidationRule $rule, string $valid, string $invalid): void
    {
        $this->assertTrue(Validator::make(['field' => $valid], ['field' => [$rule]])->passes());
        $this->assertTrue(Validator::make(['field' => $invalid], ['field' => [$rule]])->fails());
    }

    #[DataProvider('rules')]
    public function test_rule_objects_reject_non_strings(ValidationRule $rule, string $valid, string $invalid): void
    {
        $this->assertTrue(Validator::make(['field' => 12345], ['field' => [$rule]])->fails());
    }
}
