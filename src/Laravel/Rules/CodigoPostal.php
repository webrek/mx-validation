<?php

namespace Webrek\MxValidation\Laravel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webrek\MxValidation\ValueObjects\CodigoPostal as CodigoPostalValue;

class CodigoPostal implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! CodigoPostalValue::isValid($value)) {
            $fail('El campo :attribute no es un código postal válido.');
        }
    }
}
