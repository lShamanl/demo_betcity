<?php

declare(strict_types=1);

namespace App\Profile\Application\Betcity\UseCase\Edit;

use App\Profile\Domain\Betcity\Betcity;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(autowire: false)]
final readonly class Result
{
    public function __construct(public Betcity $betcity)
    {
    }
}
