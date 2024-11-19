<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimestampType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add($options['field_name'], DateTimeType::class, [
            'widget' => 'single_text',
            'label' => $options['label'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['field_name' => 'timestamp', 'label' => false])
            ->setAllowedTypes('field_name', ['string'])
            ->setAllowedTypes('label', ['string']);
    }
}
