<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Grid\FieldType;

use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

#[AutoconfigureTag(
    name: 'sylius.grid_field',
    attributes: [
        'type' => 'money',
    ]
)]
class MoneyType implements FieldTypeInterface
{
    private const TEMPLATE = '@app/admin/layout/grid/field/money.html.twig';

    public function __construct(
        private readonly DataExtractorInterface $dataExtractor,
        private readonly Environment $twig
    ) {
    }

    public function render(Field $field, mixed $data, array $options): string
    {
        $data = $this->dataExtractor->get($field, $data);

        return $this->twig->render(self::TEMPLATE, ['data' => $data, 'options' => $options]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
