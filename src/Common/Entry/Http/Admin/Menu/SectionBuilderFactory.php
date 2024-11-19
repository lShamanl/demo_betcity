<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Menu;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class SectionBuilderFactory
{
    /** @var SectionBuilderInterface[] */
    private array $sectionBuilders = [];

    /**
     * @param SectionBuilderInterface[]|iterable $sectionBuilders
     */
    public function __construct(
        #[TaggedIterator(tag: 'app.section_builder')]
        iterable $sectionBuilders,
    ) {
        foreach ($sectionBuilders as $sectionBuilder) {
            $this->sectionBuilders[] = $sectionBuilder;
        }
    }

    /**
     * @return SectionBuilderInterface[]
     */
    public function getSortedBuilders(): array
    {
        $builders = [];
        foreach ($this->sectionBuilders as $key => $sectionBuilder) {
            $builders[(int) ($sectionBuilder->getOrder() . $key)] = $sectionBuilder;
        }
        ksort($builders);

        return $builders;
    }
}
