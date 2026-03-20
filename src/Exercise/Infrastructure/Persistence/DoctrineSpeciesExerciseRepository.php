<?php

declare(strict_types=1);

namespace App\Exercise\Infrastructure\Persistence;

use App\Exercise\Domain\Model\SpeciesExercise;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineSpeciesExerciseRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(SpeciesExercise $exercise): void
    {
        $this->entityManager->persist($exercise);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?SpeciesExercise
    {
        return $this->entityManager->find(SpeciesExercise::class, $id);
    }
}
