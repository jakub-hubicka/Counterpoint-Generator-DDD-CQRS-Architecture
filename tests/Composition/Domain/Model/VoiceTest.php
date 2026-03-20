<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\Model;

use App\Composition\Domain\Event\NoteWritten;
use App\Composition\Domain\Event\VoiceAdded;
use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use App\Composition\Domain\ValueObject\VoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class VoiceTest extends TestCase
{
    public function testCreateVoiceRaisesVoiceAddedEvent(): void
    {
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $events = $voice->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(VoiceAdded::class, $events[0]);
    }

    public function testAddNoteWithinRange(): void
    {
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->pullDomainEvents(); // clear creation event

        $note = new Note(Pitch::C, 5, Duration::QUARTER);
        $voice->addNote($note);

        $this->assertSame(1, $voice->melody()->count());
        $events = $voice->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(NoteWritten::class, $events[0]);
    }

    public function testAddNoteOutOfRangeThrows(): void
    {
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);

        $this->expectException(\DomainException::class);
        $voice->addNote(new Note(Pitch::C, 2, Duration::QUARTER));
    }
}
