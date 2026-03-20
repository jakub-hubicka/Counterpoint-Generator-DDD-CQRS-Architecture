<?php

declare(strict_types=1);

namespace App\Composition\Domain\Exception;

final class CounterpointRuleViolationException extends \DomainException
{
    private string $ruleName;
    private ?int $measureIndex;

    public function __construct(string $ruleName, string $message, ?int $measureIndex = null)
    {
        $this->ruleName = $ruleName;
        $this->measureIndex = $measureIndex;

        parent::__construct(sprintf('[%s] %s', $ruleName, $message));
    }

    public function ruleName(): string
    {
        return $this->ruleName;
    }

    public function measureIndex(): ?int
    {
        return $this->measureIndex;
    }
}
