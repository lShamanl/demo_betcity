<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth\Domain\User;

use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Builder\Auth\User\UserBuilder;
use App\Tests\Tools\TestFixture;
use Doctrine\Persistence\ObjectManager;

class CommonFixture extends TestFixture
{
    public const ID = '10000';
    public const EMAIL = 'user@repository-common.email';

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withId(new UserId(self::ID))
            ->withEmail(self::EMAIL)
            ->build();
        $manager->persist($user);

        $manager->flush();
    }
}
