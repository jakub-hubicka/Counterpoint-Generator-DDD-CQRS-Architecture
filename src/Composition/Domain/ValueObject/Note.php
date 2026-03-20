<?php

declare(strict_types=1);

namespace App\Composition\Domain\ValueObject;

final readonly class Note
{
    public function __construct(
        private Pitch $pitch,
        private int $octave,
        private Duration $duration,
    ) {
        if ($octave < 1 || $octave > 8) {
            throw new \InvalidArgumentException(
                sprintf('Octave must be between 1 and 8, got %d', $octave)
            );
        }
    }

    public function pitch(): Pitch
    {
        return $this->pitch;
    }

    public function octave(): int
    {
        return $this->octave;
    }

    public function duration(): Duration
    {
        return $this->duration;
    }

    public function midiNumber(): int
    {
        return ($this->octave + 1) * 12 + $this->pitch->semitonesFromC();
    }

    public function diatonicNumber(): int
    {
        return $this->octave * 7 + $this->pitch->diatonicIndex();
    }

    public function equals(self $other): bool
    {
        return $this->pitch === $other->pitch
            && $this->octave === $other->octave
            && $this->duration === $other->duration;
    }

    public function withDuration(Duration $duration): self
    {
        return new self($this->pitch, $this->octave, $duration);
    }

    public function __toString(): string
    {
        return sprintf('%s%d(%s)', $this->pitch->value, $this->octave, $this->duration->value);
    }
}
