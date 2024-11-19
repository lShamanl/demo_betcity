<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Menu\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SyliusExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_currency_symbol', [$this, 'getCurrencySymbol']),
            new TwigFilter('sylius_locale_name', [$this, 'getLocaleName']),
        ];
    }

    public function getCurrencySymbol(mixed $val): string
    {
        return (string) $val;
    }

    public function getLocaleName(mixed $val): string
    {
        return (string) $val;
    }
}
