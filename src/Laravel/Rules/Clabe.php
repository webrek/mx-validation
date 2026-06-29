<?php

namespace Webrek\MxValidation\Laravel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webrek\MxValidation\ValueObjects\Clabe as ClabeValue;

class Clabe implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! ClabeValue::isValid($value)) {
            $fail('El campo :attribute no es una CLABE válida.');
        }
    }
}
