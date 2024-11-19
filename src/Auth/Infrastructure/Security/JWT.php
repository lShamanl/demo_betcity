<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class JWT
{
    public function __construct(
        #[Autowire('%env(resolve:JWT_SECRET_KEY)%')]
        public string $privateKey,
        #[Autowire('%env(resolve:JWT_PUBLIC_KEY)%')]
        public string $publicKey,
        #[Autowire('%env(JWT_PASSPHRASE)%')]
        public string $passPhrase,
    ) {
    }
}
