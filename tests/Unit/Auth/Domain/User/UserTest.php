<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Domain\User;

use App\Auth\Domain\User\User;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Builder\Auth\User\UserBuilder;
use App\Tests\Unit\UnitTestCase;
use DateTimeImmutable;

/**
 * @covers \App\Auth\Domain\User\User
 */
class UserTest extends UnitTestCase
{
    public function testConstruct(): void
    {
        $user = new User(
            id: $id = new UserId((string) self::$faker->numberBetween(1)),
            createdAt: $createdAt = new DateTimeImmutable(self::$faker->dateTime()->format(DATE_ATOM)),
            updatedAt: $updatedAt = new DateTimeImmutable(self::$faker->dateTime()->format(DATE_ATOM)),
        );
        self::assertSame($id, $user->getId());
        self::assertSame($createdAt, $user->getCreatedAt());
        self::assertSame($updatedAt, $user->getUpdatedAt());
    }

    public function testCreate(): void
    {
        $user = User::create(
            id: $id = new UserId((string) self::$faker->numberBetween(1)),
            createdAt: $createdAt = new DateTimeImmutable(self::$faker->dateTime()->format(DATE_ATOM)),
            updatedAt: $updatedAt = new DateTimeImmutable(self::$faker->dateTime()->format(DATE_ATOM)),
            email: $email = self::$faker->email(),
            roles: $roles = [User::ROLE_ADMIN],
            name: $name = self::$faker->name()
        );
        self::assertSame($id, $user->getId());
        self::assertSame($createdAt, $user->getCreatedAt());
        self::assertSame($updatedAt, $user->getUpdatedAt());
        self::assertSame($email, $user->getEmail());
        self::assertSame($roles, $user->getRoles());
        self::assertSame($name, $user->getName());
    }

    public function testChangePassword(): void
    {
        $oldPassword = md5(self::$faker->password());
        $newPassword = md5(self::$faker->password());
        $user = (new UserBuilder())->withPassword($oldPassword)->build();
        $user->changePassword($newPassword);
        self::assertSame($newPassword, $user->getPassword());
    }

    public function testEdit(): void
    {
        $user = (new UserBuilder())
            ->build();

        $user->edit(
            name: $name = self::$faker->name(),
            email: $email = self::$faker->email(),
            roles: $roles = [User::ROLE_ADMIN],
        );

        self::assertSame($name, $user->getName());
        self::assertSame($email, $user->getEmail());
        self::assertSame($roles, $user->getRoles());
        self::assertSame(User::ROLE_ADMIN, $user->getRole());
    }

    public function testGetId(): void
    {
        $expected = new UserId((string) self::$faker->numberBetween(1));
        $user = (new UserBuilder())->withId($expected)->build();
        self::assertSame($expected, $user->getId());
    }

    public function testGetCreatedAt(): void
    {
        $expected = new DateTimeImmutable(self::$faker->dateTime()->format('Y-m-d H:i:s'));
        $user = (new UserBuilder())->withCreatedAt($expected)->build();
        self::assertSame($expected, $user->getCreatedAt());
    }

    public function testGetUpdatedAt(): void
    {
        $expected = new DateTimeImmutable(self::$faker->dateTime()->format('Y-m-d H:i:s'));
        $user = (new UserBuilder())->withUpdatedAt($expected)->build();
        self::assertSame($expected, $user->getUpdatedAt());
    }

    public function testGetSalt(): void
    {
        $user = (new UserBuilder())->build();
        self::assertNull($user->getSalt());
    }

    public function testGetRoles(): void
    {
        $user = (new UserBuilder())->build();
        self::assertEquals([User::ROLE_ADMIN], $user->getRoles());
    }

    public function testGetEmail(): void
    {
        $expected = self::$faker->email();
        $user = (new UserBuilder())->withEmail($expected)->build();
        self::assertSame($expected, $user->getEmail());
    }

    public function testGetUserIdentifier(): void
    {
        $expected = self::$faker->email();
        $user = (new UserBuilder())->withEmail($expected)->build();
        self::assertSame($expected, $user->getUserIdentifier());
    }

    public function testGetUsername(): void
    {
        $expected = self::$faker->email();
        $user = (new UserBuilder())->withEmail($expected)->build();
        self::assertSame($expected, $user->getUsername());
    }

    public function testGetName(): void
    {
        $expected = self::$faker->name();
        $user = (new UserBuilder())->withName($expected)->build();
        self::assertSame($expected, $user->getName());
    }

    public function testOnUpdated(): void
    {
        $expected = new DateTimeImmutable(self::$faker->dateTime()->format('Y-m-d H:i:s'));
        $user = (new UserBuilder())->withUpdatedAt($expected)->build();
        $user->onUpdated();
        self::assertDatetimeNow($user->getUpdatedAt());
    }

    public function testGetPassword(): void
    {
        $expected = md5(self::$faker->password());
        $user = (new UserBuilder())->withPassword($expected)->build();
        self::assertSame($expected, $user->getPassword());
    }

    public function testGetPlainPassword(): void
    {
        $plainPassword = self::$faker->password();
        $user = (new UserBuilder())->build();
        $user->setPlainPassword($plainPassword);
        self::assertEquals($plainPassword, $user->getPlainPassword());
    }
}
