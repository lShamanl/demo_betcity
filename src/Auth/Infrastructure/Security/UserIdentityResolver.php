<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UserIdentityResolver implements ValueResolverInterface
{
    public function __construct(
        private UserIdentityFetcher $userIdentityFetcher,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        yield $this->userIdentityFetcher->fetch($request);
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();

        return null !== $type && is_subclass_of($type, UserInterface::class);
    }
}
