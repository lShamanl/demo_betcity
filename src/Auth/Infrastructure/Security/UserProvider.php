<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use App\Auth\Domain\User\User;
use App\Auth\Domain\User\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class UserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . $user::class);
        }

        return self::identityByUser(
            $this->loadUser($user->getUserIdentifier())
        );
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->loadUser($identifier);

        return self::identityByUser($user);
    }

    /**
     * @psalm-suppress MoreSpecificImplementedParamType
     *
     * @param class-string<UserInterface> $class
     */
    public function supportsClass(string $class): bool
    {
        return UserIdentity::class === $class;
    }

    private function loadUser(string $username): User
    {
        if (($user = $this->userRepository->findByEmail($username)) !== null) {
            return $user;
        }

        throw new UserNotFoundException();
    }

    public static function identityByUser(User $user): UserIdentity
    {
        return new UserIdentity(
            id: $user->getId()->getValue(),
            username: $user->getEmail(),
            password: $user->getPasswordHash(),
            display: $user->getEmail(),
            role: $user->getRole(),
        );
    }
}
