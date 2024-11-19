<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\EventSubscriber\RateLimitter;

use App\Auth\Infrastructure\Security\UserIdentityFetcher;
use App\Common\Exception\Http\ToManyRequestException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[AsEventListener(event: KernelEvents::RESPONSE, method: 'onRateLimit', priority: 80)]
readonly class ApplyRateLimitingListener
{
    private array $whiteListIp;

    public function __construct(
        private UserIdentityFetcher $userIdentityFetcher,
        /** @var RateLimiterFactory[] */
        private array $rateLimiterClassMap,
        #[Autowire('%env(resolve:RATE_LIMIT_WHITE_LIST_IP)%')]
        string $whiteListIp,
    ) {
        $this->whiteListIp = json_decode($whiteListIp, true) ?? [];
    }

    public function onRateLimit(KernelEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $ip = $request->getClientIp();
        if (null !== $ip && in_array($ip, $this->whiteListIp)) {
            return;
        }

        /** @var string $controllerClass */
        $controllerClass = $request->attributes->get('_controller');

        $routeName = $request->attributes->get('_route') ?? 'undefined';

        $rateLimiter = $this->rateLimiterClassMap[$controllerClass] ?? null;
        if (null === $rateLimiter) {
            return; // этому экшену не назначена служба ограничения количества запросов
        }

        $userIdentity = $this->userIdentityFetcher->tryFetch($request);

        if (null === $userIdentity) {
            $limit = $rateLimiter->create($routeName . ':' . $request->getClientIp())->consume();
        } else {
            $limit = $rateLimiter->create($routeName . ':' . $userIdentity->id)->consume();
        }

        if (false === $limit->isAccepted()) {
            throw new ToManyRequestException($limit);
        }
    }
}
