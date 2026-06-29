<?php

namespace Webrek\MxValidation\Laravel\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Webrek\MxValidation\ValueObjects\Rfc;

/**
 * Casts a column to an {@see Rfc} value object on read and stores the
 * normalized (uppercase, punctuation-stripped) string on write.
 *
 * @implements CastsAttributes<Rfc|null, Rfc|string|null>
 */
class RfcCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Rfc
    {
        return is_string($value) ? Rfc::tryParse($value) : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) ($value instanceof Rfc ? $value : Rfc::parse((string) $value));
    }
}
