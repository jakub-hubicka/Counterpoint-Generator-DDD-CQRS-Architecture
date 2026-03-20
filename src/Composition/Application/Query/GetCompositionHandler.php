<?php

declare(strict_types=1);

namespace App\Composition\Application\Query;

use App\Composition\Domain\Repository\CantusFirmusRepositoryInterface;
use App\Composition\Domain\Repository\VoiceRepositoryInterface;

final readonly class GetCompositionHandler
{
    public function __construct(
        private VoiceRepositoryInterface $voiceRepository,
        private CantusFirmusRepositoryInterface $cantusFirmusRepository,
    ) {
    }

    public function __invoke(GetCompositionQuery $query): array
    {
        $voices = $this->voiceRepository->findByCompositionId($query->compositionId);
        $cantusFirmus = $this->cantusFirmusRepository->findByCompositionId($query->compositionId);

        $voiceData = [];
        foreach ($voices as $voice) {
            $notes = [];
            foreach ($voice->melody()->notes() as $note) {
                $notes[] = [
                    'pitch' => $note->pitch()->value,
                    'octave' => $note->octave(),
                    'duration' => $note->duration()->value,
                ];
            }

            $voiceData[] = [
                'id' => $voice->id(),
                'voiceType' => $voice->voiceType()->value,
                'notes' => $notes,
            ];
        }

        $cfData = null;
        if ($cantusFirmus !== null) {
            $cfNotes = [];
            foreach ($cantusFirmus->melody()->notes() as $note) {
                $cfNotes[] = [
                    'pitch' => $note->pitch()->value,
                    'octave' => $note->octave(),
                    'duration' => $note->duration()->value,
                ];
            }
            $cfData = [
                'id' => $cantusFirmus->id(),
                'tonic' => $cantusFirmus->tonic()->value,
                'notes' => $cfNotes,
                'isComplete' => $cantusFirmus->isComplete(),
            ];
        }

        return [
            'compositionId' => $query->compositionId,
            'cantusFirmus' => $cfData,
            'voices' => $voiceData,
        ];
    }
}
