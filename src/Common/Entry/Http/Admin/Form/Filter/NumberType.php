<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType as CoreNumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add($options['field_name'], CoreNumberType::class, [
            'label' => $options['label'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['field_name' => 'number', 'label' => false, 'empty_data' => null])
            ->setAllowedTypes('field_name', ['string'])
            ->setAllowedTypes('label', ['string', 'bool']);
    }
}
