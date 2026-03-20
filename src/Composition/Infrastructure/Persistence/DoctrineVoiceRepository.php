<?php

declare(strict_types=1);

namespace App\Composition\Infrastructure\Persistence;

use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Repository\VoiceRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineVoiceRepository implements VoiceRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Voice $voice): void
    {
        $voice->syncMelodyToData();
        $this->entityManager->persist($voice);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Voice
    {
        return $this->entityManager->find(Voice::class, $id);
    }

    /** @return list<Voice> */
    public function findByCompositionId(string $compositionId): array
    {
        return $this->entityManager->getRepository(Voice::class)
            ->findBy(['compositionId' => $compositionId]);
    }
}
