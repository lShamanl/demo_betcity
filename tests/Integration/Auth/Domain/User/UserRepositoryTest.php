<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth\Domain\User;

use App\Auth\Domain\User\User;
use App\Auth\Domain\User\UserRepository;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Builder\Auth\User\UserBuilder;
use App\Tests\Integration\IntegrationTestCase;

/** @covers \App\Auth\Domain\User\UserRepository */
class UserRepositoryTest extends IntegrationTestCase
{
    protected static UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        self::$userRepository = self::get(UserRepository::class);
    }

    protected static function withFixtures(): array
    {
        return [
            CommonFixture::class,
        ];
    }

    public function testHasByEmail(): void
    {
        self::assertTrue(
            self::$userRepository->hasByEmail(CommonFixture::EMAIL)
        );
        self::assertFalse(
            self::$userRepository->hasByEmail(md5(random_bytes(255)))
        );
    }

    public function testAdd(): void
    {
        $user = (new UserBuilder())->build();

        self::$userRepository->add($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertEquals(
            $user->getId(),
            self::$userRepository->findById($user->getId())?->getId()
        );
    }

    public function testRemove(): void
    {
        $user = self::$userRepository->findById(
            new UserId(CommonFixture::ID)
        );
        self::assertEquals(
            expected: CommonFixture::ID,
            actual: $user?->getId()->getValue()
        );

        /** @var User $user */
        self::$userRepository->remove($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertNull(
            actual: self::$userRepository->findById(
                new UserId(CommonFixture::ID)
            )
        );
    }

    public function testFindByIdThenExists(): void
    {
        self::assertEquals(
            expected: CommonFixture::ID,
            actual: self::$userRepository->findById(
                new UserId(CommonFixture::ID)
            )?->getId()->getValue()
        );
    }

    public function testFindByIdThenNull(): void
    {
        $id = new UserId(
            (string) self::$faker->numberBetween(
                '1000000',
                '2000000',
            )
        );
        self::assertNull(
            actual: self::$userRepository->findById($id)
        );
    }
}
