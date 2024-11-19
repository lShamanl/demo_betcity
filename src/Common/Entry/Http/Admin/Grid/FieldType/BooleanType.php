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
        'type' => 'boolean',
    ]
)]
class BooleanType implements FieldTypeInterface
{
    private const TEMPLATE = '@app/admin/layout/grid/field/boolean.html.twig';
    private const TEMPLATE_INVERSE = '@app/admin/layout/grid/field/boolean_inverse.html.twig';

    /**
     * BooleanFieldType constructor.
     */
    public function __construct(
        private readonly DataExtractorInterface $dataExtractor,
        private readonly Environment $twig
    ) {
    }

    public function render(Field $field, mixed $data, array $options): string
    {
        $data = $this->dataExtractor->get($field, $data);

        // Is inverse template should be used for bool value
        $inverse = \filter_var($options['inverse'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $tpl = $inverse ? self::TEMPLATE_INVERSE : self::TEMPLATE;

        return $this->twig->render($tpl, ['data' => (bool) $data, 'options' => $options]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('inverse', false);
    }
}
