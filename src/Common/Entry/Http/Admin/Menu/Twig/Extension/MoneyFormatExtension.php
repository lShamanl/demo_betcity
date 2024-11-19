<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Menu\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MoneyFormatExtension extends AbstractExtension
{
    private const TEMPLATE = '@app/admin/layout/crud/show/_money.html.twig';
    private const AVAILABLE_POSITIONS = [
        'left',
        'right',
        'center',
    ];

    public function __construct(
        private readonly Environment $environment,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('moneyFormat', [$this, 'moneyFormat'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string[] $options
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function moneyFormat(mixed $number, array $options = []): string
    {
        $align = isset($options['align']) && in_array($options['align'], self::AVAILABLE_POSITIONS, true) ? $options['align'] : null;

        return $this->environment->render(self::TEMPLATE, [
            'data' => $number,
            'align' => $align,
        ]);
    }
}
