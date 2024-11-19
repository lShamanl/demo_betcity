<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Token\Authentication;

use App\Auth\Domain\User\UserRepository;
use App\Auth\Entry\Http\Token\TokenOutputContract;
use App\Auth\Infrastructure\Security\JwtTokenizer;
use App\Auth\Infrastructure\Security\RefreshTokenCache;
use App\Auth\Infrastructure\Security\UserIdentity;
use App\Common\RateLimiter\Attribute\RateLimiting;
use IWD\SymfonyEntryContract\Dto\Input\OutputFormat;
use IWD\SymfonyEntryContract\Dto\Output\ApiFormatter;
use IWD\SymfonyEntryContract\Service\Presenter;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class Action extends AbstractController
{
    public const NAME = 'token.authentication';

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
     *     description="Authentication",
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
    #[RateLimiting(limiter: 'auth_token_authentication')]
    #[Route(path: '/api/token/authentication', name: self::NAME, methods: ['POST'])]
    public function authentication(
        UserRepository $userRepository,
        InputContract $contract,
        UserPasswordHasherInterface $passwordHasher,
        JwtTokenizer $jwtTokenizer,
        Presenter $presenter,
        RefreshTokenCache $refreshTokenCache,
        TranslatorInterface $translator,
    ): Response {
        $user = $userRepository->findByEmail($contract->email);
        if (null === $user) {
            throw new AccessDeniedException(message: $translator->trans('app.admin.ui.modules.auth.user.flash.error_invalid_credentials'), code: 401);
        }

        if (!$passwordHasher->isPasswordValid($user, $contract->password)) {
            throw new AccessDeniedException(message: $translator->trans('app.admin.ui.modules.auth.user.flash.error_invalid_credentials'), code: 401);
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

        $refreshTokenCache->cache($userIdentity->id, $refresh);

        return $presenter->present(
            data: ApiFormatter::prepare(
                data: $outputContract,
                messages: [
                    $translator->trans('app.admin.ui.modules.auth.user.flash.success_authentication'),
                ]
            ),
            outputFormat: new OutputFormat('json')
        );
    }
}
