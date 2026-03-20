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

#[Route('/api')]
final class AnalysisController extends AbstractController
{
    #[Route('/analysis', name: 'api_analyze_composition', methods: ['POST'])]
    public function analyze(
        Request $request,
        AnalyzeCompositionHandler $handler,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        $compositionId = $data['compositionId'] ?? '';

        try {
            $query = new AnalyzeCompositionQuery($compositionId);
            $results = $handler($query);

            $output = [];
            foreach ($results as $result) {
                $output[] = $result->toArray();
            }

            return $this->json($output);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
