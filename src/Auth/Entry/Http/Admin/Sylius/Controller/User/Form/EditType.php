<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Sylius\Controller\User\Form;

use App\Auth\Domain\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app.admin.ui.modules.auth.user.properties.name',
                'constraints' => [
                    new NotBlank(allowNull: false),
                    new Length(max: 255),
                ],
                'empty_data' => '',
            ])
            ->add('email', EmailType::class, [
                'label' => 'app.admin.ui.modules.auth.user.properties.email',
                'constraints' => [
                    new Email(),
                    new NotBlank(allowNull: false),
                    new Length(max: 255),
                ],
                'empty_data' => '',
            ])
            ->add('role', ChoiceType::class, [
                'required' => true,
                'label' => 'app.admin.ui.modules.auth.user.properties.role',
                'choices' => [
                    'app.admin.ui.common.none' => null,
                    'app.admin.ui.modules.auth.user.enums.role.user' => User::ROLE_USER,
                    'app.admin.ui.modules.auth.user.enums.role.admin' => User::ROLE_ADMIN,
                ],
                'constraints' => [
                    new NotBlank(allowNull: false),
                ],
                'empty_data' => '',
                'autocomplete' => true,
            ])
        ;
    }
}
