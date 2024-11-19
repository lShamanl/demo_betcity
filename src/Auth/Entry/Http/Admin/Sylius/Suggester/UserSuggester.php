<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Sylius\Suggester;

use App\Auth\Domain\User\User;
use App\Common\Service\Suggester\SuggesterInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField(alias: self::SUGGESTER_NAME)]
class UserSuggester extends AbstractType implements SuggesterInterface
{
    private const SUGGESTER_NAME = 'auth_user';

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => User::class,
            'choice_label' => 'email',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }

    public function getSuggesterName(): string
    {
        return self::SUGGESTER_NAME;
    }
}
