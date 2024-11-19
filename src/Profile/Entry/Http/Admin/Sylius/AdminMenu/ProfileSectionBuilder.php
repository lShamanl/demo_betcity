<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Sylius\AdminMenu;

use App\Common\Entry\Http\Admin\Menu\SectionBuilderInterface;
use Knp\Menu\ItemInterface;

class ProfileSectionBuilder implements SectionBuilderInterface
{
    public function build(ItemInterface $menu): void
    {
        $settings = $menu
            ->addChild('profile')
            ->setLabel('app.admin.ui.menu.profile.label');

        $settings
            ->addChild('betcity', ['route' => 'app_profile.betcity_index'])
            ->setLabel('app.admin.ui.menu.profile.betcity.list')
            ->setLabelAttribute('icon', 'list');
    }

    public function getOrder(): int
    {
        return 20;
    }
}
