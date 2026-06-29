<?php

namespace Webrek\MxValidation\Laravel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webrek\MxValidation\ValueObjects\Curp as CurpValue;

class Curp implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! CurpValue::isValid($value)) {
            $fail('El campo :attribute no es una CURP válida.');
        }
    }
}
