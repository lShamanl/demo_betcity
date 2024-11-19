<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use IWD\SymfonyEntryContract\Dto\Output\ApiFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Serializer\SerializerInterface;

class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly JwtTokenizer $jwtTokenizer,
        private readonly SerializerInterface $serializer,
        private readonly UserProviderInterface $userProvider
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $authorization = $request->headers->get('Authorization');
        if (null === $authorization) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        try {
            if (str_starts_with($authorization, 'Bearer ') && strlen($authorization) > 7) {
                [$type, $token] = explode(' ', $authorization);
                /** @var UserIdentity $user */
                $user = $this->userProvider->loadUserByIdentifier($this->jwtTokenizer->decode($token)['username']);

                return new SelfValidatingPassport(
                    new UserBadge(
                        $user->getUserIdentifier()
                    )
                );
            }
        } catch (\Throwable $throwable) {
            throw new CustomUserMessageAuthenticationException(
                message: $throwable->getMessage(),
                previous: $throwable
            );
        }

        throw new CustomUserMessageAuthenticationException('Authorization invalid, expected Bearer');
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     */
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        $response = new Response();
        $response->setContent(
            $this->serializer->serialize(
                ApiFormatter::prepare(
                    null,
                    Response::HTTP_UNAUTHORIZED,
                    'Authentication Required'
                ),
                'json'
            )
        );
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

        return $response;
    }
}
