<?php

declare(strict_types=1);

namespace App\Composition\Domain\Repository;

use App\Composition\Domain\Model\Voice;

interface VoiceRepositoryInterface
{
    public function save(Voice $voice): void;

    public function findById(string $id): ?Voice;

    /** @return list<Voice> */
    public function findByCompositionId(string $compositionId): array;
}
