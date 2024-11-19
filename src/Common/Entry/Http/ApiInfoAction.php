<?php

declare(strict_types=1);

namespace App\Common\Entry\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

class ApiInfoAction extends AbstractController
{
    public const NAME = 'info';

    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    #[Route(path: '/api/info', name: self::NAME, methods: ['GET'])]
    public function root(): JsonResponse
    {
        return new JsonResponse([
            'api' => [
                'version' => '1.0',
            ],
        ]);
    }
}
