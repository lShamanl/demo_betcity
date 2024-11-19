<?php

declare(strict_types=1);

namespace App\Common\Entry\Http;

use App\Common\Service\Metrics\AdapterInterface;
use App\Common\Service\Metrics\MetricsGuard;
use Prometheus\RenderTextFormat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class MetricsAction extends AbstractController
{
    #[Route(
        path: '/metrics/{token?}',
        name: 'metrics',
        defaults: ['token' => ''],
        methods: ['GET', '']
    )]
    public function metrics(
        string $token,
        AdapterInterface $adapter,
        MetricsGuard $metricsGuard,
        ?UserInterface $user
    ): Response {
        if (null === $user) {
            $metricsGuard->guard($token);
        }

        return new Response(
            (new RenderTextFormat())->render($adapter->collect()),
            Response::HTTP_OK,
            ['Content-Type' => 'text/plain']
        );
    }
}
