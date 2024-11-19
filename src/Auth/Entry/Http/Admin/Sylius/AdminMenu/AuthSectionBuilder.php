<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Sylius\AdminMenu;

use App\Common\Entry\Http\Admin\Menu\SectionBuilderInterface;
use Knp\Menu\ItemInterface;

class AuthSectionBuilder implements SectionBuilderInterface
{
    public function build(ItemInterface $menu): void
    {
        $settings = $menu
            ->addChild('auth')
            ->setLabel('app.admin.ui.menu.auth.label');

        $settings
            ->addChild('user', ['route' => 'app_auth.user_index'])
            ->setLabel('app.admin.ui.menu.auth.user.list')
            ->setLabelAttribute('icon', 'list');
    }

    public function getOrder(): int
    {
        return 20;
    }
}
