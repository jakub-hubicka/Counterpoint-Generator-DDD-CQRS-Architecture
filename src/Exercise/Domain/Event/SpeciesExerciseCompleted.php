<?php

declare(strict_types=1);

namespace App\Exercise\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class SpeciesExerciseCompleted extends Event
{
    public function __construct(
        private readonly string $exerciseId,
        private readonly string $species,
    ) {
    }

    public function exerciseId(): string
    {
        return $this->exerciseId;
    }

    public function species(): string
    {
        return $this->species;
    }
}
