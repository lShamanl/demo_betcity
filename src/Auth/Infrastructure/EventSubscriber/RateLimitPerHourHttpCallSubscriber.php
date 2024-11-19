<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\EventSubscriber;

use App\Auth\Infrastructure\Security\UserIdentityFetcher;
use App\Common\Exception\Http\ToManyRequestException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[AsEventListener(event: KernelEvents::RESPONSE, method: 'onRateLimit', priority: 90)]
readonly class RateLimitPerHourHttpCallSubscriber
{
    private array $whiteListIp;

    public function __construct(
        private RateLimiterFactory $anonymousApiCommon,
        private RateLimiterFactory $authenticatedApiCommon,
        private UserIdentityFetcher $userIdentityFetcher,
        #[Autowire('%env(resolve:RATE_LIMIT_WHITE_LIST_IP)%')]
        string $whiteListIp,
    ) {
        $this->whiteListIp = json_decode($whiteListIp, true) ?? [];
    }

    public function onRateLimit(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        $ip = $request->getClientIp();
        if (null !== $ip && in_array($ip, $this->whiteListIp)) {
            return;
        }

        $userIdentity = $this->userIdentityFetcher->tryFetch($request);

        if (null === $userIdentity) {
            $limiter = $this->anonymousApiCommon->create($request->getClientIp());
        } else {
            $limiter = $this->authenticatedApiCommon->create($userIdentity->id);
        }

        $limit = $limiter->consume();

        if (false === $limit->isAccepted()) {
            throw new ToManyRequestException($limit);
        }
    }
}
