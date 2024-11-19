<?php

declare(strict_types=1);

namespace App\Common\Entry\Http\Contract;

class DeletedOutputContract
{
    public function __construct(
        public string $id = ''
    ) {
    }
}
