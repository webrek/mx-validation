<?php

namespace Webrek\MxValidation\Laravel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webrek\MxValidation\ValueObjects\Nss as NssValue;

class Nss implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! NssValue::isValid($value)) {
            $fail('El campo :attribute no es un NSS válido.');
        }
    }
}
