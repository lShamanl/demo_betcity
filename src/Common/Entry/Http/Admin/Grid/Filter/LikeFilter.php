<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Grid\Filter;

use App\Common\Entry\Http\Admin\Form\Filter\LikeType;
use Doctrine\ORM\QueryBuilder;
use ReflectionProperty;
use Sylius\Bundle\GridBundle\Doctrine\ORM\ExpressionBuilder;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(
    name: 'sylius.grid_filter',
    attributes: [
        'type' => 'like',
        'form_type' => LikeType::class,
    ]
)]
class LikeFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, mixed $data, array $options = []): void
    {
        $refProperty = new ReflectionProperty(ExpressionBuilder::class, 'queryBuilder');
        $refProperty->setAccessible(true);

        $value = '%' . mb_strtolower($data[$name]) . '%';
        $bind = 'bind_' . md5(serialize($value) . random_bytes(10));

        /** @var QueryBuilder $qb */
        $qb = $refProperty->getValue($dataSource->getExpressionBuilder());
        $rootAlias = current($qb->getRootAliases());
        $qb->andWhere('LOWER(' . $rootAlias . '.' . $name . ') LIKE :' . $bind)->setParameter($bind, $value);
    }
}
