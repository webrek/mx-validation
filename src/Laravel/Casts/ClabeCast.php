<?php

namespace Webrek\MxValidation\Laravel\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Webrek\MxValidation\ValueObjects\Clabe;

/**
 * @implements CastsAttributes<Clabe|null, Clabe|string|null>
 */
class ClabeCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Clabe
    {
        return is_string($value) ? Clabe::tryParse($value) : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) ($value instanceof Clabe ? $value : Clabe::parse((string) $value));
    }
}
