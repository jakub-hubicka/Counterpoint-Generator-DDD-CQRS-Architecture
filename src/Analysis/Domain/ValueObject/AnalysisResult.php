<?php

declare(strict_types=1);

namespace App\Analysis\Domain\ValueObject;

final readonly class AnalysisResult
{
    /**
     * @param list<array{rule: string, message: string, measureIndex: ?int}> $violations
     */
    public function __construct(
        private string $compositionId,
        private array $violations,
        private bool $valid,
    ) {
    }

    public function compositionId(): string
    {
        return $this->compositionId;
    }

    /** @return list<array{rule: string, message: string, measureIndex: ?int}> */
    public function violations(): array
    {
        return $this->violations;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function toArray(): array
    {
        return [
            'compositionId' => $this->compositionId,
            'valid' => $this->valid,
            'violations' => $this->violations,
        ];
    }
}
