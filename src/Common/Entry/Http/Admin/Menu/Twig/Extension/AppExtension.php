<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Admin\Menu\Twig\Extension;

use Mobile_Detect;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @method bool isMobile()
 */
class AppExtension extends AbstractExtension
{
    protected Mobile_Detect $detector;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->detector = new Mobile_Detect();
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('md5', 'md5'),
            new TwigFilter('floatval', 'floatval'),
        ];
    }

    /**
     * @return TwigFunction[]
     *
     * @psalm-suppress InvalidArgument
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('floatval', 'floatval'),
            new TwigFunction('is_mobile', [$this, 'isMobile']),
        ];
    }

    /**
     * Pass through calls of undefined methods to the mobile detect library.
     *
     * @psalm-suppress InvalidArgument
     */
    public function __call(string $name, array $arguments): mixed
    {
        // @phpstan-ignore-next-line
        return call_user_func_array([$this->detector, $name], $arguments);
    }
}
