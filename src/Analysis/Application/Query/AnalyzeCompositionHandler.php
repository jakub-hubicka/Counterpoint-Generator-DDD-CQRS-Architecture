<?php

declare(strict_types=1);

namespace App\Analysis\Application\Query;

use App\Analysis\Domain\Service\CounterpointAnalyzer;
use App\Analysis\Domain\ValueObject\AnalysisResult;
use App\Composition\Domain\Repository\CantusFirmusRepositoryInterface;
use App\Composition\Domain\Repository\VoiceRepositoryInterface;

final readonly class AnalyzeCompositionHandler
{
    public function __construct(
        private VoiceRepositoryInterface $voiceRepository,
        private CantusFirmusRepositoryInterface $cantusFirmusRepository,
        private CounterpointAnalyzer $analyzer,
    ) {
    }

    /** @return list<AnalysisResult> */
    public function __invoke(AnalyzeCompositionQuery $query): array
    {
        $cantusFirmus = $this->cantusFirmusRepository->findByCompositionId($query->compositionId);

        if ($cantusFirmus === null) {
            throw new \InvalidArgumentException(
                sprintf('No cantus firmus found for composition %s', $query->compositionId)
            );
        }

        $voices = $this->voiceRepository->findByCompositionId($query->compositionId);
        $results = [];

        foreach ($voices as $voice) {
            $results[] = $this->analyzer->analyze($query->compositionId, $voice, $cantusFirmus);
        }

        return $results;
    }
}
