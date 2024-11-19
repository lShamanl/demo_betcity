<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth\Application\User\UseCase\ChangePassword;

use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Builder\Auth\User\UserBuilder;
use App\Tests\Tools\TestFixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class Fixture extends TestFixture
{
    public const ID = '10002';
    public const PASSWORD = 'my-super-secret-password';

    public function __construct(
        private readonly PasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withId(new UserId(self::ID))
            ->withPassword($this->passwordHasher->hash(self::PASSWORD))
            ->build();
        $manager->persist($user);

        $manager->flush();
    }
}
