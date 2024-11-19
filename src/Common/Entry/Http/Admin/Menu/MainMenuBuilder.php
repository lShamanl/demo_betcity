<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(
    name: 'knp_menu.menu_builder',
    attributes: [
        'method' => 'buildMenu',
        'alias' => 'ec.main',
    ]
)]
class MainMenuBuilder
{
    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly SectionBuilderFactory $sectionBuilderFactory
    ) {
    }

    public function buildMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        foreach ($this->sectionBuilderFactory->getSortedBuilders() as $sectionBuilder) {
            $sectionBuilder->build($menu);
        }

        return $menu;
    }
}
