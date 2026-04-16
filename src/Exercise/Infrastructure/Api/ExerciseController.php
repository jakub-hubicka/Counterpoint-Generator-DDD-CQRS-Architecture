<?php

declare(strict_types=1);

namespace App\Exercise\Infrastructure\Api;

use App\Exercise\Application\Command\StartExerciseCommand;
use App\Exercise\Application\Command\StartExerciseHandler;
use App\Exercise\Application\Query\GetExerciseHandler;
use App\Exercise\Application\Query\GetExerciseQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api')]
final class ExerciseController extends AbstractController
{
    #[Route('/exercises', name: 'api_start_exercise', methods: ['POST'])]
    #[OA\Post(
        path: '/api/exercises',
        summary: 'Start a new exercise',
        description: 'Starts a new counterpoint exercise for a specific species',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['species', 'compositionId'],
                properties: [
                    new OA\Property(property: 'species', type: 'string', example: 'first', description: 'Species of counterpoint (first, second, etc.)'),
                    new OA\Property(property: 'compositionId', type: 'string', description: 'Composition ID to use for the exercise')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Exercise started successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string'),
                        new OA\Property(property: 'species', type: 'string'),
                        new OA\Property(property: 'compositionId', type: 'string'),
                        new OA\Property(property: 'completed', type: 'boolean')
                    ]
                )
            )
        ]
    )]
    public function startExercise(
        Request $request,
        StartExerciseHandler $handler,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];

        $command = new StartExerciseCommand(
            species: $data['species'] ?? '',
            compositionId: $data['compositionId'] ?? '',
        );

        $exercise = $handler($command);

        return $this->json([
            'id' => $exercise->id(),
            'species' => $exercise->species()->value,
            'compositionId' => $exercise->compositionId(),
            'completed' => $exercise->isCompleted(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/exercises/{id}', name: 'api_get_exercise', methods: ['GET'])]
    #[OA\Get(
        path: '/api/exercises/{id}',
        summary: 'Get an exercise',
        description: 'Retrieves details of a specific exercise',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                description: 'Exercise ID'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Exercise retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string'),
                        new OA\Property(property: 'species', type: 'string'),
                        new OA\Property(property: 'compositionId', type: 'string'),
                        new OA\Property(property: 'completed', type: 'boolean')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Exercise not found',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'error', type: 'string')]
                )
            )
        ]
    )]
    public function getExercise(
        string $id,
        GetExerciseHandler $handler,
    ): JsonResponse {
        $query = new GetExerciseQuery($id);
        $result = $handler($query);

        if ($result === null) {
            return $this->json(['error' => 'Exercise not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($result);
    }
}
