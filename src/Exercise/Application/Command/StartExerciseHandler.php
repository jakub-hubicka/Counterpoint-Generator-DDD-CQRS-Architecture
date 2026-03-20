<?php

declare(strict_types=1);

namespace App\Exercise\Application\Command;

use App\Exercise\Domain\Model\SpeciesExercise;
use App\Exercise\Domain\ValueObject\Species;
use App\Exercise\Infrastructure\Persistence\DoctrineSpeciesExerciseRepository;
use Symfony\Component\Uid\Uuid;

final readonly class StartExerciseHandler
{
    public function __construct(
        private DoctrineSpeciesExerciseRepository $exerciseRepository,
    ) {
    }

    public function __invoke(StartExerciseCommand $command): SpeciesExercise
    {
        $species = Species::from($command->species);
        $exercise = new SpeciesExercise(Uuid::v7(), $species, $command->compositionId);

        $this->exerciseRepository->save($exercise);

        return $exercise;
    }
}
