<?php

declare(strict_types=1);

namespace App\Auth\Domain\User\Service;

use App\Auth\Domain\User\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

readonly class UserNextIdService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /** @throws Exception */
    public function allocateId(): UserId
    {
        $id = $this->em
            ->getConnection()
            ->prepare("SELECT nextval('auth_user_id_seq')")
            ->executeQuery()
            ->fetchOne();

        return new UserId((string) $id);
    }
}
