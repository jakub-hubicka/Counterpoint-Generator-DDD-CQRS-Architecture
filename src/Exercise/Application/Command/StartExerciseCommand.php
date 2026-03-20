<?php

declare(strict_types=1);

namespace App\Exercise\Application\Command;

final readonly class StartExerciseCommand
{
    public function __construct(
        public string $species,
        public string $compositionId,
    ) {
    }
}
