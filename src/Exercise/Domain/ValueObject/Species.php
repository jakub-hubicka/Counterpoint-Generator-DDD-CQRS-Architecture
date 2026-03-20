<?php

declare(strict_types=1);

namespace App\Exercise\Domain\ValueObject;

enum Species: string
{
    case FIRST = 'first';
    case SECOND = 'second';
    case THIRD = 'third';
    case FOURTH = 'fourth';
    case FIFTH = 'fifth';

    public function description(): string
    {
        return match ($this) {
            self::FIRST => 'Note against note',
            self::SECOND => 'Two notes against one',
            self::THIRD => 'Four notes against one',
            self::FOURTH => 'Syncopation',
            self::FIFTH => 'Florid counterpoint',
        };
    }
}
