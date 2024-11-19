<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth\Application\User\UseCase\Create;

use App\Auth\Application\User\UseCase\Create\Command;
use App\Auth\Application\User\UseCase\Create\Handler;
use App\Auth\Application\User\UseCase\Create\ResultCase;
use App\Auth\Domain\User\User;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/** @covers \App\Auth\Application\User\UseCase\Create\Handler */
class HandlerTest extends IntegrationTestCase
{
    protected static Handler $handler;
    protected static PasswordHasherInterface $passwordHasher;

    public function setUp(): void
    {
        parent::setUp();
        self::$handler = self::get(Handler::class);
        self::$passwordHasher = self::get(PasswordHasherInterface::class);
    }

    protected static function withFixtures(): array
    {
        return [
            Fixture::class,
        ];
    }

    public function testHandleWhenSuccess(): void
    {
        $result = self::$handler->handle(
            $command = new Command(
                email: self::$faker->email() . md5(random_bytes(255)),
                plainPassword: self::$faker->password(),
                role: User::ROLE_ADMIN,
                name: self::$faker->name() . md5(random_bytes(255)),
            )
        );
        self::assertTrue(
            $result->case->isEqual(ResultCase::Success)
        );
        self::assertNotNull($result->user);
        self::assertInstanceOf(UserId::class, $result->user->getId());
        self::assertSame($command->name, $result->user->getName());
        self::assertSame($command->email, $result->user->getEmail());
        self::assertSame([User::ROLE_ADMIN], $result->user->getRoles());
        self::assertDatetimeNow($result->user->getCreatedAt());
        self::assertDatetimeNow($result->user->getUpdatedAt());
        self::assertTrue(
            self::$passwordHasher->verify(
                $result->user->getPassword(),
                $command->plainPassword
            )
        );
    }

    public function testHandleWhenEmailIsBusy(): void
    {
        $result = self::$handler->handle(
            new Command(
                email: Fixture::EMAIL,
                plainPassword: self::$faker->password(),
                role: User::ROLE_ADMIN,
                name: self::$faker->name() . md5(random_bytes(255)),
            )
        );
        self::assertTrue(
            $result->case->isEqual(ResultCase::EmailIsBusy)
        );
        self::assertNull($result->user);
    }
}
