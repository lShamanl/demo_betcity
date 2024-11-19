<?php

declare(strict_types=1);

namespace App\Tests\Tools;

use Doctrine\Persistence\ObjectManager;

abstract class TestFixture
{
    abstract public function load(ObjectManager $manager): void;
}
