<?php

declare(strict_types=1);

namespace App\Profile\Domain\Betcity;

use App\Profile\Domain\Betcity\ValueObject\BetcityId;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Model\ResourceInterface;

class BetcityRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Betcity::class));
    }

    public function add(ResourceInterface $resource): void
    {
        $this->getEntityManager()->persist($resource);
    }

    public function remove(ResourceInterface $resource): void
    {
        $this->getEntityManager()->remove($resource);
    }

    public function findById(BetcityId $id): ?Betcity
    {
        /** @var Betcity|null $betcity */
        $betcity = $this->findOneBy(['id' => $id]);

        return $betcity;
    }

    public function findByUserId(int $userId): ?Betcity
    {
        /** @var Betcity|null $betcity */
        $betcity = $this->findOneBy(['userId' => $userId]);

        return $betcity;
    }

    public function hasById(BetcityId $id): bool
    {
        return null !== $this->findOneBy(['id' => $id]);
    }

    public function hasByUserId(int $userId): bool
    {
        return null !== $this->findOneBy(['userId' => $userId]);
    }

    public function all(
        int $size = 100,
        int $offset = 0,
    ): Generator {
        $count = $this->createQueryBuilder('betcity')->select('count(1)')
            ->getQuery()
            ->getSingleScalarResult();

        while ($offset < $count) {
            /** @var Betcity[] $betcities */
            $betcities = $this->createQueryBuilder('betcity')
                ->addOrderBy('betcity.id', 'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($size)
                ->getQuery()
                ->getResult()
            ;
            foreach ($betcities as $betcity) {
                yield $betcity;
            }

            $offset += $size;
            $this->getEntityManager()->clear();
        }
    }
}
