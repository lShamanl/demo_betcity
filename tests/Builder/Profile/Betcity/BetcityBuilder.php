<?php

declare(strict_types=1);

namespace App\Tests\Builder\Profile\Betcity;

use App\Profile\Domain\Betcity\Betcity;
use App\Profile\Domain\Betcity\Enum\Gender;
use App\Profile\Domain\Betcity\ValueObject\BetcityId;
use App\Tests\Builder\AbstractBuilder;
use DateInterval;
use DateTimeImmutable;

class BetcityBuilder extends AbstractBuilder
{
    protected BetcityId $id;

    protected DateTimeImmutable $createdAt;

    protected DateTimeImmutable $updatedAt;

    protected int $userId;

    protected ?string $name;

    protected Gender $gender;

    public function build(): Betcity
    {
        /** @var Betcity $betcity */
        $betcity = self::create($this);

        return $betcity;
    }

    /** @return class-string<Betcity> */
    public static function getEntityClass(): string
    {
        return Betcity::class;
    }

    public static function randomPayload(object $entity): array
    {
        $payload = [];

        $payload['id'] = new BetcityId((string) self::$faker->numberBetween(100000, 9999999));
        $payload['createdAt'] = (new DateTimeImmutable())->sub(new DateInterval('P' . random_int(180, 365) . 'D'));
        $payload['updatedAt'] = (new DateTimeImmutable())->sub(new DateInterval('P' . random_int(1, 179) . 'D'));
        $payload['userId'] = self::$faker->numberBetween(-32768, 32767);
        $payload['name'] = self::$faker->text(255);
        $payload['gender'] = self::$faker->randomElement(Gender::cases());

        return $payload;
    }

    public function withId(BetcityId $id): self
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

    public function withUserId(int $userId): self
    {
        $clone = clone $this;
        $clone->userId = $userId;

        return $clone;
    }

    public function withName(?string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    public function withGender(Gender $gender): self
    {
        $clone = clone $this;
        $clone->gender = $gender;

        return $clone;
    }
}
