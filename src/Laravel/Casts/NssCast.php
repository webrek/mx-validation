<?php

namespace Webrek\MxValidation\Laravel\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Webrek\MxValidation\ValueObjects\Nss;

/**
 * @implements CastsAttributes<Nss|null, Nss|string|null>
 */
class NssCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Nss
    {
        return is_string($value) ? Nss::tryParse($value) : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) ($value instanceof Nss ? $value : Nss::parse((string) $value));
    }
}
