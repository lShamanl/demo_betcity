<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Sylius\Controller\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'app.admin.ui.modules.auth.user.other_labels.old_password',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new UserPassword(),
                    new NotBlank(allowNull: false),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'app.admin.ui.modules.auth.user.properties.password',
                'invalid_message' => $this->translator->trans('app.admin.ui.modules.auth.user.flash.password_is_not_equals'),
                'required' => true,
                'first_options' => [
                    'label' => 'app.admin.ui.modules.auth.user.properties.password',
                    'constraints' => [
                        new NotBlank(allowNull: false),
                        new Length([
                            'min' => 6,
                            'minMessage' => $this->translator->trans('app.admin.ui.modules.auth.user.flash.password_is_short'),
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'app.admin.ui.modules.auth.user.actions.password_repeat',
                    'constraints' => [
                        new NotBlank(allowNull: false),
                        new Length([
                            'min' => 6,
                            'minMessage' => $this->translator->trans('app.admin.ui.modules.auth.user.flash.password_is_short'),
                            'max' => 4096,
                        ]),
                    ],
                ],
            ]);
    }
}
