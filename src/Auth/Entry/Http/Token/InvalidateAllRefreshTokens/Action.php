<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Token\InvalidateAllRefreshTokens;

use App\Auth\Infrastructure\Security\RefreshTokenCache;
use App\Auth\Infrastructure\Security\UserIdentity;
use IWD\SymfonyEntryContract\Dto\Input\OutputFormat;
use IWD\SymfonyEntryContract\Dto\Output\ApiFormatter;
use IWD\SymfonyEntryContract\Service\Presenter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Action extends AbstractController
{
    /**
     * @OA\Tag(name="Auth.Token")
     * @OA\Response(
     *     response=200,
     *     description="Refresh token",
     *     @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref=@Model(type=ApiFormatter::class)),
     *              @OA\Schema(type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      type="object",
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      example="200"
     *                 )
     *             )
     *         }
     *     )
     * )
     * @Security(name="Bearer")
     */
    #[Route(
        path: '/api/token/invalidate-all-refresh-tokens',
        name: 'token.invalidateAllRefreshTokens',
        methods: ['POST']
    )]
    public function invalidateRefreshToken(
        Presenter $presenter,
        RefreshTokenCache $refreshTokenCache,
        UserIdentity $user
    ): Response {
        $refreshTokenCache->invalidateAll($user->id);

        return $presenter->present(
            data: ApiFormatter::prepare(
                messages: ['Invalidated done']
            ),
            outputFormat: new OutputFormat('json')
        );
    }
}
