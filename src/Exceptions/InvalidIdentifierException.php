<?php

namespace Webrek\MxValidation\Exceptions;

use InvalidArgumentException;

class InvalidIdentifierException extends InvalidArgumentException
{
    public static function for(string $type, string $value): self
    {
        return new self("The value [{$value}] is not a valid {$type}.");
    }
}
