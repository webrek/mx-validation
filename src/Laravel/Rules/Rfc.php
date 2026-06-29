<?php

namespace Webrek\MxValidation\Laravel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webrek\MxValidation\ValueObjects\Rfc as RfcValue;

class Rfc implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! RfcValue::isValid($value)) {
            $fail('El campo :attribute no es un RFC válido.');
        }
    }
}
