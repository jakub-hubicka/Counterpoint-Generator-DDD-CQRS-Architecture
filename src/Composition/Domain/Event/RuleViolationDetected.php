<?php

declare(strict_types=1);

namespace App\Composition\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class RuleViolationDetected extends Event
{
    public function __construct(
        private readonly string $ruleName,
        private readonly string $message,
        private readonly ?int $measureIndex = null,
    ) {
    }

    public function ruleName(): string
    {
        return $this->ruleName;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function measureIndex(): ?int
    {
        return $this->measureIndex;
    }
}
