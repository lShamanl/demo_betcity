<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Sylius\Controller\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MainInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app.admin.ui.modules.auth.user.properties.name',
                'required' => true,
                'constraints' => [
                    new NotBlank(allowNull: false),
                    new Length(max: 255),
                ],
                'empty_data' => '',
            ])
            ->add('email', EmailType::class, [
                'label' => 'app.admin.ui.modules.auth.user.properties.email',
                'required' => true,
                'constraints' => [
                    new Email(),
                    new NotBlank(allowNull: false),
                    new Length(max: 255),
                ],
                'empty_data' => '',
            ]);
    }
}
