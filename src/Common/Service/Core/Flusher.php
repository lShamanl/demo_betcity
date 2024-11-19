<?php

declare(strict_types=1);

namespace App\Common\Service\Core;

use Doctrine\ORM\EntityManagerInterface;

readonly class Flusher
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function flush(): void
    {
        $this->em->flush();
    }
}
