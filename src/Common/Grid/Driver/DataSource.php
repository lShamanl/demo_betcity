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

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Sylius\Bundle\GridBundle\Doctrine\ORM\ExpressionBuilder;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Parameters;

final readonly class DataSource implements DataSourceInterface
{
    private ExpressionBuilderInterface $expressionBuilder;

    /**
     * @param bool $fetchJoinCollection must be 'true' when the query fetch-joins a to-many collection,
     *                                  otherwise the pagination will yield incorrect results
     *                                  https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/pagination.html
     * @param bool $useOutputWalkers    must be 'true' if the query has an order by statement for a field from
     *                                  the to-many association, otherwise it will throw an exception
     *                                  might greatly affect the performance (https://github.com/Sylius/Sylius/issues/3775)
     */
    public function __construct(
        private QueryBuilder $queryBuilder,
        private bool $fetchJoinCollection,
        private bool $useOutputWalkers
    ) {
        $this->expressionBuilder = new ExpressionBuilder($queryBuilder);
    }

    public function restrict($expression, string $condition = DataSourceInterface::CONDITION_AND): void
    {
        switch ($condition) {
            case DataSourceInterface::CONDITION_AND:
                $this->queryBuilder->andWhere($expression);

                break;
            case DataSourceInterface::CONDITION_OR:
                $this->queryBuilder->orWhere($expression);

                break;
        }
    }

    public function getExpressionBuilder(): ExpressionBuilderInterface
    {
        return $this->expressionBuilder;
    }

    public function getData(Parameters $parameters)
    {
        $paginator = new AppPagerFanta(
            new QueryAdapter($this->queryBuilder, $this->fetchJoinCollection, $this->useOutputWalkers)
        );
        $paginator->setNbResults(1000);
        $paginator->setNormalizeOutOfRangePages(true);
        $paginator->setCurrentPage((int) $parameters->get('page', 1));

        return $paginator;
    }
}
