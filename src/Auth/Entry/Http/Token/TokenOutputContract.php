<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Token;

class TokenOutputContract
{
    public string $access;
    public string $refresh;

    public static function create(string $access, string $refresh): self
    {
        $output = new self();
        $output->access = $access;
        $output->refresh = $refresh;

        return $output;
    }
}
