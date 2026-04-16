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
use OpenApi\Attributes as OA;

#[Route('/api')]
final class VoiceController extends AbstractController
{
    #[Route('/compositions', name: 'api_create_composition', methods: ['POST'])]
    #[OA\Post(
        path: '/api/compositions',
        summary: 'Create a new composition',
        description: 'Creates a new composition with a cantus firmus',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['tonic'],
                properties: [
                    new OA\Property(property: 'tonic', type: 'string', example: 'C', description: 'The tonic pitch of the cantus firmus')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Composition created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'compositionId', type: 'string', example: '018f1234-5678-89ab-cdef-0123456789ab'),
                        new OA\Property(property: 'cantusFirmus', properties: [
                            new OA\Property(property: 'id', type: 'string'),
                            new OA\Property(property: 'tonic', type: 'string')
                        ], type: 'object')
                    ]
                )
            )
        ]
    )]
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
    #[OA\Post(
        path: '/api/compositions/{id}/voices',
        summary: 'Add a voice to a composition',
        description: 'Adds a new voice (counterpoint) to an existing composition',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                description: 'Composition ID'
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['voiceType'],
                properties: [
                    new OA\Property(property: 'voiceType', type: 'string', example: 'soprano', description: 'Type of voice to add')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Voice added successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string'),
                        new OA\Property(property: 'compositionId', type: 'string'),
                        new OA\Property(property: 'voiceType', type: 'string')
                    ]
                )
            )
        ]
    )]
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
    #[OA\Post(
        path: '/api/voices/{id}/notes',
        summary: 'Write a note to a voice',
        description: 'Adds a note with pitch, octave, and duration to a voice',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                description: 'Voice ID'
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['pitch', 'octave', 'duration'],
                properties: [
                    new OA\Property(property: 'pitch', type: 'string', example: 'C', description: 'Note pitch'),
                    new OA\Property(property: 'octave', type: 'integer', example: 4, description: 'Octave number'),
                    new OA\Property(property: 'duration', type: 'string', example: 'quarter', description: 'Note duration')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Note written successfully',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'status', type: 'string', example: 'ok')]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid note data',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'error', type: 'string')]
                )
            )
        ]
    )]
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
    #[OA\Get(
        path: '/api/compositions/{id}',
        summary: 'Get a composition',
        description: 'Retrieves a composition with all its voices and notes',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                description: 'Composition ID'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Composition retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string'),
                        new OA\Property(property: 'cantusFirmus', type: 'object'),
                        new OA\Property(property: 'voices', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function getComposition(
        string $id,
        GetCompositionHandler $handler,
    ): JsonResponse {
        $query = new GetCompositionQuery($id);
        $result = $handler($query);

        return $this->json($result);
    }
}
