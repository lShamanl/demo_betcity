<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth\Application\User\UseCase\Edit;

use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Builder\Auth\User\UserBuilder;
use App\Tests\Tools\TestFixture;
use Doctrine\Persistence\ObjectManager;

class Fixture extends TestFixture
{
    public const ID = '10003';
    public const SELF_EMAIL = 'user@handler.edit.self-email';
    public const BUSY_EMAIL = 'user@handler.edit.busy-email';

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withId(new UserId(self::ID))
            ->withEmail(self::SELF_EMAIL)
            ->build();
        $otherUser = (new UserBuilder())
            ->withEmail(self::BUSY_EMAIL)
            ->build();

        $manager->persist($user);
        $manager->persist($otherUser);

        $manager->flush();
    }
}
