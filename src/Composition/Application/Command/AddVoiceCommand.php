<?php

declare(strict_types=1);

namespace App\Composition\Application\Command;

final readonly class AddVoiceCommand
{
    public function __construct(
        public string $compositionId,
        public string $voiceType,
    ) {
    }
}
