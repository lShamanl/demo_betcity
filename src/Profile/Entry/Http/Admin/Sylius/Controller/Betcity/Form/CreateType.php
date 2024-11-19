<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Sylius\Controller\Betcity\Form;

use App\Profile\Domain\Betcity\Enum\Gender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder
            ->add('userId', IntegerType::class, [
                'required' => true,
                'label' => 'app.admin.ui.modules.profile.betcity.properties.user_id',
                'constraints' => [
                    new NotBlank(allowNull: false),
                ],
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'app.admin.ui.modules.profile.betcity.properties.name',
                'constraints' => [
                    new Length(max: 255),
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'required' => true,
                'label' => 'app.admin.ui.modules.profile.betcity.properties.gender',
                'choices' => [
                    'app.admin.ui.common.none' => null,
                    'app.admin.ui.modules.profile.betcity.enums.gender.secret' => Gender::Secret,
                    'app.admin.ui.modules.profile.betcity.enums.gender.male' => Gender::Male,
                    'app.admin.ui.modules.profile.betcity.enums.gender.female' => Gender::Female,
                ],
                'constraints' => [
                    new Choice(choices: Gender::cases()),
                    new NotBlank(allowNull: false),
                ],
                'empty_data' => '',
                'autocomplete' => true,
            ]);
    }
}
