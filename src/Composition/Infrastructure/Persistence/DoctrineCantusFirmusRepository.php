<?php

declare(strict_types=1);

namespace App\Composition\Infrastructure\Persistence;

use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Repository\CantusFirmusRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineCantusFirmusRepository implements CantusFirmusRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(CantusFirmus $cantusFirmus): void
    {
        $cantusFirmus->syncMelodyToData();
        $this->entityManager->persist($cantusFirmus);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?CantusFirmus
    {
        return $this->entityManager->find(CantusFirmus::class, $id);
    }

    public function findByCompositionId(string $compositionId): ?CantusFirmus
    {
        return $this->entityManager->getRepository(CantusFirmus::class)
            ->findOneBy(['compositionId' => $compositionId]);
    }
}
