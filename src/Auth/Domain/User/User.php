<?php

declare(strict_types=1);

namespace App\Auth\Domain\User;

use App\Auth\Domain\User\Event\UserCreatedEvent;
use App\Auth\Domain\User\Type\UserIdType;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Common\Service\Core\AggregateRoot;
use App\Common\Service\Core\EventsTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'auth_users')]
#[UniqueEntity(fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, ResourceInterface, PasswordAuthenticatedUserInterface, AggregateRoot
{
    use EventsTrait;

    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\Column(type: UserIdType::NAME, nullable: false)]
    private UserId $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'json', options: ['default' => '[]'])]
    private array $userRoles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    private ?string $plainPassword = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    public function __construct(
        UserId $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        $this->recordEvent(
            new UserCreatedEvent(
                id: $this->id,
            )
        );
    }

    public static function create(
        UserId $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        string $email,
        array $roles,
        string $name,
    ): self {
        $user = new self(
            id: $id,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
        $user->email = $email;
        $user->userRoles = $roles;
        $user->name = $name;

        return $user;
    }

    public function edit(string $name, string $email, array $roles): void
    {
        $this->email = $email;
        $this->name = $name;
        $this->userRoles = $roles;
    }

    public function changePassword(string $password): void
    {
        $this->password = $password;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->userRoles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function onUpdated(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @internal
     */
    public function setUserRoles(array $userRoles): void
    {
        $this->userRoles = $userRoles;
    }

    /**
     * @internal
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @internal
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = '';
    }

    /**
     * @internal
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    #todo: переделать под множество ролей
    public function getRole(): string
    {
        return current($this->userRoles);
    }

    /**
     * @internal
     */
    public function getPasswordHash(): string
    {
        return $this->password;
    }
}
