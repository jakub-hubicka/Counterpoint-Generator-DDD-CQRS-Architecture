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

#[Route('/api')]
final class ExerciseController extends AbstractController
{
    #[Route('/exercises', name: 'api_start_exercise', methods: ['POST'])]
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
