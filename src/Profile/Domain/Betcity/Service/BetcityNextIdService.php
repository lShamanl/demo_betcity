<?php

declare(strict_types=1);

namespace App\Profile\Domain\Betcity\Service;

use App\Profile\Domain\Betcity\ValueObject\BetcityId;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

readonly class BetcityNextIdService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /** @throws Exception */
    public function allocateId(): BetcityId
    {
        $id = $this->em
            ->getConnection()
            ->prepare("SELECT nextval('profile_betcity_id_seq')")
            ->executeQuery()
            ->fetchOne();

        return new BetcityId((string) $id);
    }
}
