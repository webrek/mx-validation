<?php

namespace Webrek\MxValidation\Tests;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Webrek\MxValidation\Faker\MxProvider;

/**
 * Base for the framework-agnostic core tests — plain PHPUnit, no Laravel.
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * A Faker generator with the package provider attached, for tests that
     * generate sample identifiers.
     */
    protected function faker(): Generator
    {
        $faker = Factory::create();
        $faker->addProvider(new MxProvider($faker));
        $faker->seed(2026);

        return $faker;
    }
}
