<?php

declare(strict_types=1);

namespace App\Composition\Domain\Model;

use App\Composition\Domain\ValueObject\Note;

final class Melody
{
    /** @var list<Note> */
    private array $notes = [];

    public function addNote(Note $note): void
    {
        $this->notes[] = $note;
    }

    /** @return list<Note> */
    public function notes(): array
    {
        return $this->notes;
    }

    public function noteAt(int $index): ?Note
    {
        return $this->notes[$index] ?? null;
    }

    public function count(): int
    {
        return count($this->notes);
    }

    public function isEmpty(): bool
    {
        return count($this->notes) === 0;
    }

    public function lastNote(): ?Note
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->notes[count($this->notes) - 1];
    }

    /** @param list<Note> $notes */
    public static function fromNotes(array $notes): self
    {
        $melody = new self();
        foreach ($notes as $note) {
            $melody->addNote($note);
        }
        return $melody;
    }
}
