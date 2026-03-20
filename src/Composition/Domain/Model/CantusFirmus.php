<?php

declare(strict_types=1);

namespace App\Composition\Domain\Model;

use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use Symfony\Component\Uid\Uuid;

class CantusFirmus
{
    private string $id;
    private string $compositionId;
    private Pitch $tonic;
    private string $tonicValue;
    private Melody $melody;
    private array $notesData = [];
    private \DateTimeImmutable $createdAt;

    /** @var list<object> */
    private array $domainEvents = [];

    public function __construct(Uuid $id, string $compositionId, Pitch $tonic)
    {
        $this->id = (string) $id;
        $this->compositionId = $compositionId;
        $this->tonic = $tonic;
        $this->tonicValue = $tonic->value;
        $this->melody = new Melody();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function compositionId(): string
    {
        return $this->compositionId;
    }

    public function tonic(): Pitch
    {
        return $this->tonic;
    }

    public function melody(): Melody
    {
        return $this->melody;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function addNote(Note $note): void
    {
        $notes = $this->melody->notes();

        // First note must be the tonic
        if (count($notes) === 0 && $note->pitch() !== $this->tonic) {
            throw new \DomainException(
                sprintf('Cantus firmus must start on the tonic (%s), got %s', $this->tonic->value, $note->pitch()->value)
            );
        }

        // Enforce stepwise motion: only seconds between consecutive notes
        if (count($notes) > 0) {
            $lastNote = $notes[count($notes) - 1];
            $interval = new Interval($lastNote, $note);

            if (!$interval->isStep()) {
                throw new \DomainException(
                    sprintf(
                        'Cantus firmus allows only stepwise motion (seconds). Got interval of %s between %s and %s',
                        $interval->name(),
                        (string) $lastNote,
                        (string) $note,
                    )
                );
            }
        }

        $this->melody->addNote($note);
    }

    public function isComplete(): bool
    {
        $notes = $this->melody->notes();
        if (count($notes) < 2) {
            return false;
        }

        $lastNote = $notes[count($notes) - 1];
        return $lastNote->pitch() === $this->tonic;
    }

    /** @return list<object> */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    public function syncMelodyToData(): void
    {
        $this->tonicValue = $this->tonic->value;
        $this->notesData = array_map(
            fn(Note $note) => [
                'pitch' => $note->pitch()->value,
                'octave' => $note->octave(),
                'duration' => $note->duration()->value,
            ],
            $this->melody->notes()
        );
    }

    public function restoreMelodyFromData(): void
    {
        $this->tonic = Pitch::from($this->tonicValue);
        $this->melody = new Melody();
        foreach ($this->notesData as $data) {
            $this->melody->addNote(new Note(
                Pitch::from($data['pitch']),
                $data['octave'],
                Duration::from($data['duration']),
            ));
        }
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
