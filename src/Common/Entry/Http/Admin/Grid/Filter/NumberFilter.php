<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Grid\Filter;

use App\Common\Entry\Http\Admin\Form\Filter\NumberType;
use Exception;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(
    name: 'sylius.grid_filter',
    attributes: [
        'type' => 'number',
        'form_type' => NumberType::class,
    ]
)]
class NumberFilter implements FilterInterface
{
    /**
     * @throws Exception
     */
    public function apply(DataSourceInterface $dataSource, string $name, mixed $data, array $options = []): void
    {
        $value = trim($data);

        if ('' === $value) {
            return;
        }

        $dataSource->restrict(
            $dataSource
                ->getExpressionBuilder()
                ->andX(
                    $dataSource->getExpressionBuilder()->equals(
                        $name,
                        str_replace(',', '.', $value)
                    ),
                )
        );
    }
}
