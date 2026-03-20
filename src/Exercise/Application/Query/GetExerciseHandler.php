<?php

declare(strict_types=1);

namespace App\Exercise\Application\Query;

use App\Exercise\Infrastructure\Persistence\DoctrineSpeciesExerciseRepository;

final readonly class GetExerciseHandler
{
    public function __construct(
        private DoctrineSpeciesExerciseRepository $exerciseRepository,
    ) {
    }

    public function __invoke(GetExerciseQuery $query): array
    {
        $exercise = $this->exerciseRepository->findById($query->exerciseId);

        if ($exercise === null) {
            throw new \InvalidArgumentException(
                sprintf('Exercise with id %s not found', $query->exerciseId)
            );
        }

        return [
            'id' => $exercise->id(),
            'species' => $exercise->species()->value,
            'speciesDescription' => $exercise->species()->description(),
            'compositionId' => $exercise->compositionId(),
            'completed' => $exercise->isCompleted(),
            'feedback' => $exercise->feedback(),
        ];
    }
}
