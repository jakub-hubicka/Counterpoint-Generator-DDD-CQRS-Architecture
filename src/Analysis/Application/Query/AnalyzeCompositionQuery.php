<?php

declare(strict_types=1);

namespace App\Analysis\Application\Query;

final readonly class AnalyzeCompositionQuery
{
    public function __construct(
        public string $compositionId,
    ) {
    }
}
