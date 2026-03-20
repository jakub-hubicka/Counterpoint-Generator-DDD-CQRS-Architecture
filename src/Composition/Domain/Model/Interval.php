<?php

declare(strict_types=1);

namespace App\Composition\Domain\Model;

use App\Composition\Domain\ValueObject\Note;

final readonly class Interval
{
    private int $semitones;
    private int $diatonicSteps;

    public function __construct(Note $lower, Note $upper)
    {
        $this->semitones = abs($upper->midiNumber() - $lower->midiNumber());
        $this->diatonicSteps = abs($upper->diatonicNumber() - $lower->diatonicNumber());
    }

    public function semitones(): int
    {
        return $this->semitones;
    }

    public function diatonicSteps(): int
    {
        return $this->diatonicSteps;
    }

    public function name(): string
    {
        return match ($this->diatonicSteps) {
            0 => 'unison',
            1 => 'second',
            2 => 'third',
            3 => 'fourth',
            4 => 'fifth',
            5 => 'sixth',
            6 => 'seventh',
            7 => 'octave',
            default => sprintf('compound(%d)', $this->diatonicSteps),
        };
    }

    public function isPerfect(): bool
    {
        return in_array($this->diatonicSteps, [0, 3, 4, 7], true);
    }

    public function isConsonant(): bool
    {
        return in_array($this->diatonicSteps, [0, 2, 4, 5, 7], true)
            || ($this->diatonicSteps === 3 && $this->semitones === 5);
    }

    public function isDissonant(): bool
    {
        return !$this->isConsonant();
    }

    public function isStep(): bool
    {
        return $this->diatonicSteps <= 1;
    }

    public function isLeap(): bool
    {
        return $this->diatonicSteps > 1;
    }

    public function isLargerThanSixth(): bool
    {
        return $this->diatonicSteps > 5;
    }
}
