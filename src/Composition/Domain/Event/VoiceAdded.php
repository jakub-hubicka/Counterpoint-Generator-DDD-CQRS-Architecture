<?php

declare(strict_types=1);

namespace App\Composition\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class VoiceAdded extends Event
{
    public function __construct(
        private readonly string $voiceId,
        private readonly string $voiceType,
    ) {
    }

    public function voiceId(): string
    {
        return $this->voiceId;
    }

    public function voiceType(): string
    {
        return $this->voiceType;
    }
}
