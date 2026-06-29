<?php

namespace Webrek\MxValidation\Tests;

use Faker\Factory;
use Faker\Generator;
use Orchestra\Testbench\TestCase as Orchestra;
use Webrek\MxValidation\Faker\MxProvider;
use Webrek\MxValidation\Laravel\MxValidationServiceProvider;

/**
 * Base for the Laravel-bridge tests (rules, casts, service provider).
 */
abstract class LaravelTestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [MxValidationServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function faker(): Generator
    {
        $faker = Factory::create();
        $faker->addProvider(new MxProvider($faker));
        $faker->seed(2026);

        return $faker;
    }
}
