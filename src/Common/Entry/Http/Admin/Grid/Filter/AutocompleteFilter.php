<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Grid\Filter;

use App\Common\Entry\Http\Admin\Form\Filter\AutocompleteType;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(
    name: 'sylius.grid_filter',
    attributes: [
        'type' => 'autocomplete',
        'form_type' => AutocompleteType::class,
    ]
)]
class AutocompleteFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, mixed $data, array $options = []): void
    {
        if (!is_array($data) || empty($data[$name]['autocomplete'])) {
            return;
        }

        $dataSource->restrict($dataSource->getExpressionBuilder()->equals($name, (int) $data[$name]['autocomplete']));
    }
}
