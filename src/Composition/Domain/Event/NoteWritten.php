<?php

declare(strict_types=1);

namespace App\Composition\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class NoteWritten extends Event
{
    public function __construct(
        private readonly string $voiceId,
        private readonly string $pitch,
        private readonly int $octave,
        private readonly string $duration,
    ) {
    }

    public function voiceId(): string
    {
        return $this->voiceId;
    }

    public function pitch(): string
    {
        return $this->pitch;
    }

    public function octave(): int
    {
        return $this->octave;
    }

    public function duration(): string
    {
        return $this->duration;
    }
}
