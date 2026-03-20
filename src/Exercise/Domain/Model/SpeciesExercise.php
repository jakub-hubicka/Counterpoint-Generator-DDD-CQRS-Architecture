<?php

declare(strict_types=1);

namespace App\Exercise\Domain\Model;

use App\Exercise\Domain\Event\SpeciesExerciseCompleted;
use App\Exercise\Domain\ValueObject\Species;
use Symfony\Component\Uid\Uuid;

class SpeciesExercise
{
    private string $id;
    private Species $species;
    private string $speciesValue;
    private string $compositionId;
    private bool $completed;
    private ?string $feedback;
    private \DateTimeImmutable $createdAt;

    /** @var list<object> */
    private array $domainEvents = [];

    public function __construct(Uuid $id, Species $species, string $compositionId)
    {
        $this->id = (string) $id;
        $this->species = $species;
        $this->speciesValue = $species->value;
        $this->compositionId = $compositionId;
        $this->completed = false;
        $this->feedback = null;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function species(): Species
    {
        return $this->species;
    }

    public function compositionId(): string
    {
        return $this->compositionId;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function feedback(): ?string
    {
        return $this->feedback;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function complete(string $feedback): void
    {
        $this->completed = true;
        $this->feedback = $feedback;

        $this->recordEvent(new SpeciesExerciseCompleted($this->id, $this->species->value));
    }

    /** @return list<object> */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    public function syncToData(): void
    {
        $this->speciesValue = $this->species->value;
    }

    public function restoreFromData(): void
    {
        $this->species = Species::from($this->speciesValue);
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
