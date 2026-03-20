<?php

declare(strict_types=1);

namespace App\Exercise\Application\Query;

final readonly class GetExerciseQuery
{
    public function __construct(
        public string $exerciseId,
    ) {
    }
}
