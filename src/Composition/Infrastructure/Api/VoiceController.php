<?php

declare(strict_types=1);

namespace App\Composition\Infrastructure\Api;

use App\Composition\Application\Command\AddVoiceCommand;
use App\Composition\Application\Command\AddVoiceHandler;
use App\Composition\Application\Command\WriteNoteCommand;
use App\Composition\Application\Command\WriteNoteHandler;
use App\Composition\Application\Query\GetCompositionHandler;
use App\Composition\Application\Query\GetCompositionQuery;
use App\Composition\Domain\Repository\CantusFirmusRepositoryInterface;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\ValueObject\Pitch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api')]
final class VoiceController extends AbstractController
{
    #[Route('/compositions', name: 'api_create_composition', methods: ['POST'])]
    public function createComposition(
        Request $request,
        CantusFirmusRepositoryInterface $cfRepository,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        $compositionId = (string) Uuid::v7();
        $tonic = Pitch::from($data['tonic'] ?? 'C');

        $cf = new CantusFirmus(Uuid::v7(), $compositionId, $tonic);
        $cfRepository->save($cf);

        return $this->json([
            'compositionId' => $compositionId,
            'cantusFirmus' => [
                'id' => $cf->id(),
                'tonic' => $cf->tonic()->value,
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/compositions/{id}/voices', name: 'api_add_voice', methods: ['POST'])]
    public function addVoice(
        string $id,
        Request $request,
        AddVoiceHandler $handler,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];

        $command = new AddVoiceCommand(
            compositionId: $id,
            voiceType: $data['voiceType'] ?? '',
        );

        $voice = $handler($command);

        return $this->json([
            'id' => $voice->id(),
            'compositionId' => $voice->compositionId(),
            'voiceType' => $voice->voiceType()->value,
        ], Response::HTTP_CREATED);
    }

    #[Route('/voices/{id}/notes', name: 'api_write_note', methods: ['POST'])]
    public function writeNote(
        string $id,
        Request $request,
        WriteNoteHandler $handler,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];

        try {
            $command = new WriteNoteCommand(
                voiceId: $id,
                pitch: $data['pitch'] ?? '',
                octave: (int) ($data['octave'] ?? 0),
                duration: $data['duration'] ?? '',
            );

            $handler($command);

            return $this->json(['status' => 'ok'], Response::HTTP_CREATED);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/compositions/{id}', name: 'api_get_composition', methods: ['GET'])]
    public function getComposition(
        string $id,
        GetCompositionHandler $handler,
    ): JsonResponse {
        $query = new GetCompositionQuery($id);
        $result = $handler($query);

        return $this->json($result);
    }
}
