<?php

declare(strict_types=1);

namespace App\Profile\Domain\Betcity;

use App\Common\Service\Core\AggregateRoot;
use App\Common\Service\Core\EventsTrait;
use App\Profile\Domain\Betcity\Enum\Gender;
use App\Profile\Domain\Betcity\Type\BetcityIdType;
use App\Profile\Domain\Betcity\Type\GenderType;
use App\Profile\Domain\Betcity\ValueObject\BetcityId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;
use Sylius\Component\Resource\Model\ResourceInterface;

/** Клиент системы */
#[Table(name: 'profile_betcities')]
#[Entity(repositoryClass: BetcityRepository::class)]
#[HasLifecycleCallbacks]
class Betcity implements AggregateRoot, ResourceInterface
{
    use EventsTrait;

    /** Entity ID */
    #[Id]
    #[Column(
        type: BetcityIdType::NAME,
        nullable: false,
    )]
    private BetcityId $id;

    /** Entity created at */
    #[Column(
        type: 'datetime_immutable',
        unique: false,
        nullable: false,
    )]
    private DateTimeImmutable $createdAt;

    /** Entity updated at */
    #[Column(
        type: 'datetime_immutable',
        unique: false,
        nullable: false,
    )]
    private DateTimeImmutable $updatedAt;

    /** Внешний ID из контекста Auth */
    #[Column(
        type: 'integer',
        unique: true,
        nullable: false,
    )]
    private int $userId;

    /** Имя клиента */
    #[Column(
        type: 'string',
        unique: false,
        nullable: true,
    )]
    private ?string $name;

    /** Пол пользователя */
    #[Column(
        type: GenderType::NAME,
        unique: false,
        nullable: false,
        enumType: Gender::class,
        options: ['default' => Gender::Secret->value],
    )]
    private Gender $gender;

    public function __construct(
        BetcityId $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        int $userId,
        ?string $name,
        Gender $gender,
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->userId = $userId;
        $this->name = $name;
        $this->gender = $gender;
    }

    public function edit(
        int $userId,
        ?string $name,
        Gender $gender,
    ): void {
        $this->userId = $userId;
        $this->name = $name;
        $this->gender = $gender;
    }

    public function getId(): BetcityId
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    #[PreUpdate]
    public function onUpdated(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
