<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use App\Auth\Domain\User\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class UserIdentityFetcher
{
    public function __construct(
        private Security $security,
        private UserProviderInterface $userProvider,
        private JwtTokenizer $jwtTokenizer,
    ) {
    }

    public function tryFetch(Request $request): ?UserIdentity
    {
        try {
            return $this->fetch($request);
        } catch (AccessDeniedException $accessDeniedException) {
            return null;
        }
    }

    public function fetch(Request $request): UserIdentity
    {
        $userIdentity = null;

        if (($user = $this->security->getUser()) !== null) {
            if ($user instanceof User) {
                $userIdentity = UserProvider::identityByUser($user);
            }
            if ($user instanceof UserIdentity) {
                $userIdentity = $user;
            }
            if (null === $userIdentity) {
                throw new AccessDeniedException('Can not resolve user by request signature');
            }
        }
        if (!isset($userIdentity)) {
            return $this->getFromBearerToken($request);
        }

        return $userIdentity;
    }

    private function getFromBearerToken(Request $request): UserIdentity
    {
        $authorization = $request->headers->get('Authorization');
        if (null === $authorization) {
            throw new AccessDeniedException('Authorization token not found');
        }
        if (!(str_contains($authorization, 'Bearer ') && strlen($authorization) > 7)) {
            throw new AccessDeniedException('Invalid authorization token');
        }
        [$type, $token] = explode(' ', $authorization);

        /** @var UserIdentity $userIdentity */
        $userIdentity = $this->userProvider->loadUserByIdentifier($this->jwtTokenizer->decode($token)['username']);

        return $userIdentity;
    }
}
