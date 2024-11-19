<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Form\Filter;

use App\Common\Entry\Http\Admin\Grid\Filter\IntegerFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntegerFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!isset($options['type'])) {
            $builder
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        'sylius.ui.contains' => IntegerFilter::TYPE_CONTAINS,
                        'sylius.ui.not_contains' => IntegerFilter::TYPE_NOT_CONTAINS,
                        'sylius.ui.equal' => IntegerFilter::TYPE_EQUAL,
                        'sylius.ui.not_equal' => IntegerFilter::TYPE_NOT_EQUAL,
                        'sylius.ui.empty' => IntegerFilter::TYPE_EMPTY,
                        'sylius.ui.not_empty' => IntegerFilter::TYPE_NOT_EMPTY,
                        'sylius.ui.starts_with' => IntegerFilter::TYPE_STARTS_WITH,
                        'sylius.ui.ends_with' => IntegerFilter::TYPE_ENDS_WITH,
                        'sylius.ui.in' => IntegerFilter::TYPE_IN,
                        'sylius.ui.not_in' => IntegerFilter::TYPE_NOT_IN,
                    ],
                ])
            ;
        }

        $builder
            ->add('value', TextType::class, [
                'required' => false,
                'label' => $options['label'] ?? 'sylius.ui.value',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'label' => null,
            ])
            ->setDefined('type')
            ->setAllowedValues('type', [
                IntegerFilter::TYPE_CONTAINS,
                IntegerFilter::TYPE_NOT_CONTAINS,
                IntegerFilter::TYPE_EQUAL,
                IntegerFilter::TYPE_NOT_EQUAL,
                IntegerFilter::TYPE_EMPTY,
                IntegerFilter::TYPE_NOT_EMPTY,
                IntegerFilter::TYPE_STARTS_WITH,
                IntegerFilter::TYPE_ENDS_WITH,
                IntegerFilter::TYPE_IN,
                IntegerFilter::TYPE_NOT_IN,
            ])
            ->setDefined('label')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_grid_filter_string';
    }
}
