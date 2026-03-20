<?php

declare(strict_types=1);

namespace App\Composition\Application\Query;

final readonly class GetCompositionQuery
{
    public function __construct(
        public string $compositionId,
    ) {
    }
}
