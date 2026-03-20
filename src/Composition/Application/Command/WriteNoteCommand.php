<?php

declare(strict_types=1);

namespace App\Composition\Application\Command;

final readonly class WriteNoteCommand
{
    public function __construct(
        public string $voiceId,
        public string $pitch,
        public int $octave,
        public string $duration,
    ) {
    }
}
