<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Tests\Tools\AssertsTrait;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UnitTestCase extends KernelTestCase
{
    use AssertsTrait;

    protected static Generator $faker;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::bootKernel();
        self::$faker = Factory::create();
    }
}
