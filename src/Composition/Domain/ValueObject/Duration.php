<?php

declare(strict_types=1);

namespace App\Composition\Domain\ValueObject;

enum Duration: string
{
    case WHOLE = 'whole';
    case HALF = 'half';
    case QUARTER = 'quarter';
    case EIGHTH = 'eighth';

    public function beats(): float
    {
        return match ($this) {
            self::WHOLE => 4.0,
            self::HALF => 2.0,
            self::QUARTER => 1.0,
            self::EIGHTH => 0.5,
        };
    }
}
