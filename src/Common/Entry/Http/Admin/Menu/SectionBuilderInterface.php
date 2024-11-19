<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Menu;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.section_builder')]
interface SectionBuilderInterface
{
    public function build(ItemInterface $menu): void;

    public function getOrder(): int;
}
