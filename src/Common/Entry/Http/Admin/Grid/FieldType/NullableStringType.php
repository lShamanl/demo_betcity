<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Grid\FieldType;

use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Можно использовать для связей, которые nullable.
 */
#[AutoconfigureTag(
    name: 'sylius.grid_field',
    attributes: [
        'type' => 'nullable_string',
    ]
)]
class NullableStringType implements FieldTypeInterface
{
    public function __construct(
        private readonly DataExtractorInterface $dataExtractor,
        private readonly PropertyAccessorInterface $propertyAccessor
    ) {
    }

    public function render(Field $field, mixed $data, array $options): string
    {
        if ($this->propertyAccessor->isReadable($data, $field->getPath())) {
            return (string) $this->dataExtractor->get($field, $data);
        }

        // связь не существует
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
