<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth\Application\User\UseCase\Edit;

use App\Auth\Application\User\UseCase\Edit\Command;
use App\Auth\Application\User\UseCase\Edit\Handler;
use App\Auth\Application\User\UseCase\Edit\ResultCase;
use App\Auth\Domain\User\User;
use App\Auth\Domain\User\UserRepository;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Integration\IntegrationTestCase;

/** @covers \App\Auth\Application\User\UseCase\Edit\Handler */
class HandlerTest extends IntegrationTestCase
{
    protected static Handler $handler;
    protected static UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        self::$handler = self::get(Handler::class);
        self::$userRepository = self::get(UserRepository::class);
    }

    protected static function withFixtures(): array
    {
        return [
            Fixture::class,
        ];
    }

    public function testHandleWithNullPayloadWhenSuccess(): void
    {
        $user = self::$userRepository->findById($wallId = new UserId(Fixture::ID));
        self::assertNotNull($user);
        $expectedName = $user->getName();
        $expectedEmail = $user->getEmail();
        $expectedRole = $user->getRole();

        $result = self::$handler->handle(
            new Command(
                id: $wallId,
                name: null,
                email: null,
                role: null,
            )
        );
        self::assertTrue(
            $result->case->isEqual(ResultCase::Success)
        );
        self::assertNotNull($result->user);
        self::assertInstanceOf(UserId::class, $result->user->getId());
        self::assertSame($expectedName, $result->user->getName());
        self::assertSame($expectedEmail, $result->user->getEmail());
        self::assertSame($expectedRole, $result->user->getRole());
    }

    public function testHandleWhenSuccess(): void
    {
        $result = self::$handler->handle(
            $command = new Command(
                id: new UserId(Fixture::ID),
                name: self::$faker->name() . self::$faker->sha1(),
                email: self::$faker->email() . self::$faker->sha1(),
                role: User::ROLE_ADMIN,
            )
        );
        self::assertTrue(
            $result->case->isEqual(ResultCase::Success)
        );
        self::assertNotNull($result->user);
        self::assertInstanceOf(UserId::class, $result->user->getId());
        self::assertSame($command->name, $result->user->getName());
        self::assertSame($command->email, $result->user->getEmail());
        self::assertSame($command->role, $result->user->getRole());
    }

    public function testHandleWhenUserNotExists(): void
    {
        $result = self::$handler->handle(
            new Command(
                id: new UserId('100000'),
                name: self::$faker->name() . self::$faker->sha1(),
                email: self::$faker->email() . self::$faker->sha1(),
                role: User::ROLE_ADMIN,
            )
        );
        self::assertTrue(
            $result->case->isEqual(ResultCase::UserNotExists)
        );
        self::assertNull($result->user);
    }

    public function testHandleWhenSuccessWithSelfEmail(): void
    {
        $result = self::$handler->handle(
            $command = new Command(
                id: new UserId(Fixture::ID),
                name: self::$faker->name() . self::$faker->sha1(),
                email: Fixture::SELF_EMAIL,
                role: User::ROLE_ADMIN,
            )
        );
        self::assertTrue(
            $result->case->isEqual(ResultCase::Success)
        );
        self::assertNotNull($result->user);
        self::assertInstanceOf(UserId::class, $result->user->getId());
        self::assertSame($command->name, $result->user->getName());
        self::assertSame($command->email, $result->user->getEmail());
    }

    public function testHandleWhenEmailIsBusy(): void
    {
        $result = self::$handler->handle(
            new Command(
                id: new UserId(Fixture::ID),
                name: self::$faker->name() . self::$faker->sha1(),
                email: Fixture::BUSY_EMAIL,
                role: User::ROLE_ADMIN,
            )
        );
        self::assertTrue(
            $result->case->isEqual(ResultCase::EmailIsBusy)
        );
        self::assertNull($result->user);
    }
}
