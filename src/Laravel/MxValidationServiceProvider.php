<?php

namespace Webrek\MxValidation\Laravel;

use Faker\Generator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\ServiceProvider;
use Webrek\MxValidation\Faker\MxProvider;
use Webrek\MxValidation\ValueObjects\Clabe;
use Webrek\MxValidation\ValueObjects\CodigoPostal;
use Webrek\MxValidation\ValueObjects\Curp;
use Webrek\MxValidation\ValueObjects\Nss;
use Webrek\MxValidation\ValueObjects\Rfc;

class MxValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRules();
        $this->registerFaker();
    }

    private function registerRules(): void
    {
        /** @var Factory $factory */
        $factory = $this->app->make(Factory::class);

        $factory->extend('rfc', fn ($attribute, $value): bool => is_string($value) && Rfc::isValid($value), 'El campo :attribute no es un RFC válido.');
        $factory->extend('curp', fn ($attribute, $value): bool => is_string($value) && Curp::isValid($value), 'El campo :attribute no es una CURP válida.');
        $factory->extend('clabe', fn ($attribute, $value): bool => is_string($value) && Clabe::isValid($value), 'El campo :attribute no es una CLABE válida.');
        $factory->extend('nss', fn ($attribute, $value): bool => is_string($value) && Nss::isValid($value), 'El campo :attribute no es un NSS válido.');
        $factory->extend('codigo_postal', fn ($attribute, $value): bool => is_string($value) && CodigoPostal::isValid($value), 'El campo :attribute no es un código postal válido.');
    }

    private function registerFaker(): void
    {
        if (! class_exists(Generator::class)) {
            return;
        }

        $add = fn (Generator $faker) => $faker->addProvider(new MxProvider($faker));

        if ($this->app->resolved(Generator::class)) {
            $add($this->app->make(Generator::class));
        }

        $this->app->afterResolving(Generator::class, $add);
    }
}
