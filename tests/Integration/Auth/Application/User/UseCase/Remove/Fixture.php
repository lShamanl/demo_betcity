<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth\Application\User\UseCase\Remove;

use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Builder\Auth\User\UserBuilder;
use App\Tests\Tools\TestFixture;
use Doctrine\Persistence\ObjectManager;

class Fixture extends TestFixture
{
    public const ID = '10002';

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withId(new UserId(self::ID))
            ->build();
        $manager->persist($user);

        $manager->flush();
    }
}
