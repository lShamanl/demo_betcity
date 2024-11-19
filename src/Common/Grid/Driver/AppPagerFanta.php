<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Common\Grid\Driver;

use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use ReflectionClass;

final class AppPagerFanta extends Pagerfanta
{
    public function setNbResults(mixed $value): void
    {
        $reflectionClass = new ReflectionClass(Pagerfanta::class);
        $property = $reflectionClass->getProperty('nbResults');
        $property->setAccessible(true);
        $property->setValue($this, $value);
    }

    public function setMaxPerPage(int $maxPerPage): PagerfantaInterface
    {
        parent::setMaxPerPage($maxPerPage);
        $this->setNbResults(1000);

        return $this;
    }
}
