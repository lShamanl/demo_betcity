<?php

declare(strict_types=1);

namespace App\Tests\Builder\Auth\User;

use App\Auth\Domain\User\User;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Tests\Builder\AbstractBuilder;
use DateTimeImmutable;

class UserBuilder extends AbstractBuilder
{
    protected UserId $id;
    protected DateTimeImmutable $createdAt;
    protected DateTimeImmutable $updatedAt;
    protected string $email;
    protected string $name;
    protected array $userRoles;
    protected string $password;

    public function build(): User
    {
        /** @var User $user */
        $user = self::create($this);

        return $user;
    }

    /** @return class-string<User> */
    public static function getEntityClass(): string
    {
        return User::class;
    }

    public static function randomPayload(object $entity): array
    {
        $payload = [];

        $payload['id'] = new UserId((string) self::$faker->numberBetween(100000, 999999));
        $payload['createdAt'] = new DateTimeImmutable(self::$faker->dateTime()->format('Y-m-d H:i:s'));
        $payload['updatedAt'] = new DateTimeImmutable(self::$faker->dateTime()->format('Y-m-d H:i:s'));
        $payload['email'] = self::$faker->email();
        $payload['name'] = self::$faker->name();
        $payload['userRoles'] = [User::ROLE_ADMIN];
        $payload['password'] = sha1(self::$faker->password());

        return $payload;
    }

    public function withId(UserId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    public function withCreatedAt(DateTimeImmutable $createdAt): self
    {
        $clone = clone $this;
        $clone->createdAt = $createdAt;

        return $clone;
    }

    public function withUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $clone = clone $this;
        $clone->updatedAt = $updatedAt;

        return $clone;
    }

    public function withEmail(string $email): self
    {
        $clone = clone $this;
        $clone->email = $email;

        return $clone;
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    public function withUserRoles(array $userRoles): self
    {
        $clone = clone $this;
        $clone->userRoles = $userRoles;

        return $clone;
    }

    public function withPassword(string $password): self
    {
        $clone = clone $this;
        $clone->password = $password;

        return $clone;
    }
}
