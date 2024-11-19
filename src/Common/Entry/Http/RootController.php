<?php

declare(strict_types=1);

namespace App\Common\Entry\Http;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RootController
{
    #[Route(path: '', methods: ['GET'])]
    public function getRoot(UrlGeneratorInterface $urlGenerator): Response
    {
        return new RedirectResponse(
            $urlGenerator->generate('app_auth.user_index')
        );
    }
}
