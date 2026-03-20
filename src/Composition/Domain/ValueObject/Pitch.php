<?php

declare(strict_types=1);

namespace App\Composition\Domain\ValueObject;

enum Pitch: string
{
    case C = 'C';
    case D = 'D';
    case E = 'E';
    case F = 'F';
    case G = 'G';
    case A = 'A';
    case B = 'B';

    public function semitonesFromC(): int
    {
        return match ($this) {
            self::C => 0,
            self::D => 2,
            self::E => 4,
            self::F => 5,
            self::G => 7,
            self::A => 9,
            self::B => 11,
        };
    }

    public function diatonicIndex(): int
    {
        return match ($this) {
            self::C => 0,
            self::D => 1,
            self::E => 2,
            self::F => 3,
            self::G => 4,
            self::A => 5,
            self::B => 6,
        };
    }
}
