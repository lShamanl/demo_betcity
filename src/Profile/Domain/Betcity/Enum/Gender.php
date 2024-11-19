<?php

declare(strict_types=1);

namespace App\Profile\Domain\Betcity\Enum;

enum Gender: string
{
    case Secret = 'secret';
    case Male = 'male';
    case Female = 'female';

    public function isEqual(self $enum): bool
    {
        return $this->value === $enum->value;
    }

    /** @param self[] $enums */
    public function isEqualAtLeastOne(array $enums): bool
    {
        foreach ($enums as $enum) {
            if ($this->value === $enum->value) {
                return true;
            }
        }

        return false;
    }

    public static function states(): array
    {
        return array_column(self::cases(), 'value');
    }
}
