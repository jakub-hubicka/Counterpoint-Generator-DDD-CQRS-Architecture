<?php

declare(strict_types=1);

namespace App\Analysis\Infrastructure\Api;

use App\Analysis\Application\Query\AnalyzeCompositionHandler;
use App\Analysis\Application\Query\AnalyzeCompositionQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api')]
final class AnalysisController extends AbstractController
{
    #[Route('/analysis', name: 'api_analyze_composition', methods: ['POST'])]
    #[OA\Post(
        path: '/api/analysis',
        summary: 'Analyze a composition',
        description: 'Analyzes a composition for counterpoint rule violations',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['compositionId'],
                properties: [
                    new OA\Property(property: 'compositionId', type: 'string', description: 'Composition ID to analyze')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Analysis completed successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'rule', type: 'string'),
                        new OA\Property(property: 'passed', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            )
        ),
            new OA\Response(
                response: 400,
                description: 'Bad request - invalid JSON or missing/invalid fields',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'error', type: 'string')]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Composition not found',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'error', type: 'string')]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Unprocessable entity - domain validation failed',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'error', type: 'string')]
                )
            )
        ]
    )]
    public function analyze(
        Request $request,
        AnalyzeCompositionHandler $handler,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->json(['error' => 'Invalid JSON in request body'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['compositionId']) || $data['compositionId'] === '') {
            return $this->json(['error' => 'Missing required field: compositionId'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $query = new AnalyzeCompositionQuery($data['compositionId']);
            $results = $handler($query);

            $output = [];
            foreach ($results as $result) {
                $output[] = $result->toArray();
            }

            return $this->json($output);
        } catch (\ValueError $e) {
            return $this->json(['error' => 'Invalid value: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
