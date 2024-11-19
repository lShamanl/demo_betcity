<?php

declare(strict_types=1);

namespace App\Common\Service\Suggester;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.suggester')]
interface SuggesterInterface
{
    public function getSuggesterName(): string;
}
