<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Token\Refresh;

use App\Auth\Domain\User\UserRepository;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Auth\Entry\Http\Token\TokenOutputContract;
use App\Auth\Infrastructure\Security\JwtTokenizer;
use App\Auth\Infrastructure\Security\RefreshTokenCache;
use App\Auth\Infrastructure\Security\UserIdentity;
use IWD\SymfonyEntryContract\Dto\Input\OutputFormat;
use IWD\SymfonyEntryContract\Dto\Output\ApiFormatter;
use IWD\SymfonyEntryContract\Service\Presenter;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Action extends AbstractController
{
    /**
     * @OA\Tag(name="Auth.Token")
     * @OA\Post(
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 ref=@Model(type=InputContract::class)
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Refresh token",
     *     @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref=@Model(type=ApiFormatter::class)),
     *              @OA\Schema(type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref=@Model(type=TokenOutputContract::class)
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      example="200"
     *                 )
     *             )
     *         }
     *     )
     * )
     */
    #[Route(
        path: '/api/token/refresh',
        name: 'token.refresh',
        methods: ['POST']
    )]
    public function refresh(
        UserRepository $userRepository,
        InputContract $contract,
        JwtTokenizer $jwtTokenizer,
        Presenter $presenter,
        RefreshTokenCache $refreshTokenCache,
    ): Response {
        try {
            $userId = $jwtTokenizer->getUserIdByRefreshToken($contract->refreshToken);

            if (false === $refreshTokenCache->validate($userId, $contract->refreshToken)) {
                throw new AccessDeniedException(message: 'Token is not valid', code: 400);
            }

            $user = $userRepository->findById(new UserId($userId));
            if (null === $user) {
                throw new AccessDeniedException(message: 'User not found', code: 400);
            }

            $userIdentity = new UserIdentity(
                id: $user->getId()->getValue(),
                username: $user->getEmail(),
                password: $user->getPasswordHash(),
                display: $user->getEmail(),
                role: $user->getRole(),
            );

            $outputContract = TokenOutputContract::create(
                access: $jwtTokenizer->generateAccessToken($userIdentity),
                refresh: $refresh = $jwtTokenizer->generateRefreshToken($userIdentity)
            );

            $refreshTokenCache->invalidateAndCache($userId, $contract->refreshToken, $refresh);

            return $presenter->present(
                data: ApiFormatter::prepare(
                    data: $outputContract,
                    messages: ['Token refreshed']
                ),
                outputFormat: new OutputFormat('json')
            );
        } catch (AccessDeniedException $accessDeniedException) {
            throw new AccessDeniedException(message: 'Access denied', previous: $accessDeniedException, code: 400);
        }
    }
}
