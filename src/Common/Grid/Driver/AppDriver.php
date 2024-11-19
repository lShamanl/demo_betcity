<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Common\Grid\Driver;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(
    name: 'sylius.grid_driver',
    attributes: [
        'alias' => self::NAME,
    ]
)]
final readonly class AppDriver implements DriverInterface
{
    public const NAME = 'doctrine/no_count';

    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
    }

    public function getDataSource(array $configuration, Parameters $parameters): DataSourceInterface
    {
        if (!array_key_exists('class', $configuration)) {
            throw new InvalidArgumentException('"class" must be configured.');
        }

        /** @var ObjectManager $manager */
        $manager = $this->managerRegistry->getManagerForClass($configuration['class']);

        /** @var EntityRepository $repository */
        /** @phpstan-ignore-next-line */
        $repository = $manager->getRepository($configuration['class']);

        $fetchJoinCollection = $configuration['pagination']['fetch_join_collection'] ?? true;
        $useOutputWalkers = $configuration['pagination']['use_output_walkers'] ?? true;

        if (!isset($configuration['repository']['method'])) {
            /** @psalm-suppress UndefinedInterfaceMethod */
            return new DataSource($repository->createQueryBuilder('o'), $fetchJoinCollection, $useOutputWalkers);
        }

        $arguments = isset($configuration['repository']['arguments']) ? array_values($configuration['repository']['arguments']) : [];
        $method = $configuration['repository']['method'];
        if (is_array($method) && 2 === count($method)) {
            $queryBuilder = $method[0];
            $method = $method[1];

            return new DataSource($queryBuilder->$method(...$arguments), $fetchJoinCollection, $useOutputWalkers);
        }

        return new DataSource($repository->$method(...$arguments), $fetchJoinCollection, $useOutputWalkers);
    }
}
