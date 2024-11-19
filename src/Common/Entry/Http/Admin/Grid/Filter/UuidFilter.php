<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Grid\Filter;

use App\Common\Entry\Http\Admin\Form\Filter\UuidType;
use Exception;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Uid\Uuid;

#[AutoconfigureTag(
    name: 'sylius.grid_filter',
    attributes: [
        'type' => 'uuid',
        'form_type' => UuidType::class,
    ]
)]
class UuidFilter implements FilterInterface
{
    /**
     * @throws Exception
     */
    public function apply(DataSourceInterface $dataSource, string $name, mixed $data, array $options = []): void
    {
        $uuid = trim($data[$name]);

        if ('' === $uuid) {
            return;
        }
        if (!Uuid::isValid($uuid)) {
            return;
        }

        $dataSource->restrict(
            $dataSource
                ->getExpressionBuilder()
                ->andX(
                    $dataSource->getExpressionBuilder()->equals(
                        $name,
                        $uuid
                    ),
                )
        );
    }
}
