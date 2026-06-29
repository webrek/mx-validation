<?php

namespace Webrek\MxValidation\Laravel\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Webrek\MxValidation\ValueObjects\Curp;

/**
 * @implements CastsAttributes<Curp|null, Curp|string|null>
 */
class CurpCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Curp
    {
        return is_string($value) ? Curp::tryParse($value) : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) ($value instanceof Curp ? $value : Curp::parse((string) $value));
    }
}
