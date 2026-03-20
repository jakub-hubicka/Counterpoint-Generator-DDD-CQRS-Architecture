<?php

declare(strict_types=1);

namespace App\Composition\Domain\Repository;

use App\Composition\Domain\Model\CantusFirmus;

interface CantusFirmusRepositoryInterface
{
    public function save(CantusFirmus $cantusFirmus): void;

    public function findById(string $id): ?CantusFirmus;

    public function findByCompositionId(string $compositionId): ?CantusFirmus;
}
