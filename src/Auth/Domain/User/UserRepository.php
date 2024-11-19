<?php

declare(strict_types=1);

namespace App\Auth\Domain\User;

use App\Auth\Domain\User\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Model\ResourceInterface;

class UserRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(User::class));
    }

    public function add(ResourceInterface $resource): void
    {
        $this->getEntityManager()->persist($resource);
    }

    public function remove(ResourceInterface $resource): void
    {
        $this->getEntityManager()->remove($resource);
    }

    public function findById(UserId $id): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['id' => $id]);

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['email' => $email]);

        return $user;
    }

    public function hasById(UserId $id): bool
    {
        return null !== $this->findOneBy(['id' => $id]);
    }

    public function hasByEmail(string $email): bool
    {
        return null !== $this->findOneBy(['email' => $email]);
    }

    public function all(
        int $size = 100,
        int $offset = 0,
    ): Generator {
        $count = $this->createQueryBuilder('user')->select('count(1)')
            ->getQuery()
            ->getSingleScalarResult();

        while ($offset < $count) {
            /** @var User[] $users */
            $users = $this->createQueryBuilder('user')
                ->addOrderBy('user.id', 'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($size)
                ->getQuery()
                ->getResult()
            ;
            foreach ($users as $user) {
                yield $user;
            }

            $offset += $size;
            $this->getEntityManager()->clear();
        }
    }
}
