<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Form\Filter;

use App\Common\Service\Suggester\SuggesterInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutocompleteType extends AbstractType
{
    /**
     * @var array<string, SuggesterInterface>
     */
    private array $suggesters = [];

    public function __construct(
        #[TaggedIterator(tag: 'app.suggester')]
        iterable $suggesters
    ) {
        foreach ($suggesters as $suggester) {
            if (!$suggester instanceof SuggesterInterface) {
                continue;
            }
            /** @var SuggesterInterface $suggester */
            $this->suggesters[$suggester->getSuggesterName()] = $suggester;
        }
    }

    /**
     * @throws RuntimeException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var string $rawSuggesterName */
        $rawSuggesterName = $options['suggester'];
        $suggesterName = str_replace('-', '_', $rawSuggesterName);
        if (!isset($this->suggesters[$suggesterName])) {
            throw new RuntimeException(sprintf('There is no suggester with name %s', $rawSuggesterName));
        }

        $fieldName = $options['field_name'] ?? '';

        $builder->add($fieldName, $this->suggesters[$suggesterName]::class, [
            'label' => $options['label'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(['field_name', 'label', 'suggester'])
            ->setAllowedTypes('field_name', ['string'])
            ->setAllowedTypes('suggester', ['string'])
            ->setAllowedTypes('label', ['string'])
        ;
    }
}
