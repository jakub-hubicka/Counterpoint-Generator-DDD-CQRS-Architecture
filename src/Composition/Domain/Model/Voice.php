<?php

declare(strict_types=1);

namespace App\Composition\Domain\Model;

use App\Composition\Domain\Event\NoteWritten;
use App\Composition\Domain\Event\VoiceAdded;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use App\Composition\Domain\ValueObject\VoiceType;
use Symfony\Component\Uid\Uuid;

class Voice
{
    private string $id;
    private string $compositionId;
    private VoiceType $voiceType;
    private string $voiceTypeValue;
    private Melody $melody;
    private array $notesData = [];
    private \DateTimeImmutable $createdAt;

    /** @var list<object> */
    private array $domainEvents = [];

    public function __construct(Uuid $id, string $compositionId, VoiceType $voiceType)
    {
        $this->id = (string) $id;
        $this->compositionId = $compositionId;
        $this->voiceType = $voiceType;
        $this->voiceTypeValue = $voiceType->value;
        $this->melody = new Melody();
        $this->createdAt = new \DateTimeImmutable();

        $this->recordEvent(new VoiceAdded((string) $id, $voiceType->value));
    }

    public function id(): string
    {
        return $this->id;
    }

    public function compositionId(): string
    {
        return $this->compositionId;
    }

    public function voiceType(): VoiceType
    {
        return $this->voiceType;
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
        if (!$this->voiceType->isInRange($note)) {
            throw new \DomainException(
                sprintf(
                    'Note %s is out of range for %s voice',
                    (string) $note,
                    $this->voiceType->value
                )
            );
        }

        $this->melody->addNote($note);

        $this->recordEvent(new NoteWritten(
            $this->id,
            $note->pitch()->value,
            $note->octave(),
            $note->duration()->value,
        ));
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
        $this->voiceTypeValue = $this->voiceType->value;
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
        $this->voiceType = VoiceType::from($this->voiceTypeValue);
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
