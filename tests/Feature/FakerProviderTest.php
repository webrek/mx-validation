<?php

namespace Webrek\MxValidation\Tests\Feature;

use Faker\Generator;
use Webrek\MxValidation\Tests\LaravelTestCase;
use Webrek\MxValidation\ValueObjects\Clabe;
use Webrek\MxValidation\ValueObjects\Curp;
use Webrek\MxValidation\ValueObjects\Nss;
use Webrek\MxValidation\ValueObjects\Rfc;

class FakerProviderTest extends LaravelTestCase
{
    public function test_the_provider_is_registered_on_the_container_faker(): void
    {
        $faker = $this->app->make(Generator::class);

        $this->assertTrue(Rfc::isValid($faker->rfc()));
        $this->assertTrue(Rfc::isValid($faker->rfc(moral: true)));
        $this->assertTrue(Curp::isValid($faker->curp()));
        $this->assertTrue(Clabe::isValid($faker->clabe()));
        $this->assertTrue(Nss::isValid($faker->nss()));
    }
}
