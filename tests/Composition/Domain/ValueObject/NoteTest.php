<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\ValueObject;

use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use PHPUnit\Framework\TestCase;

final class NoteTest extends TestCase
{
    public function testCreateNote(): void
    {
        $note = new Note(Pitch::C, 4, Duration::QUARTER);

        $this->assertSame(Pitch::C, $note->pitch());
        $this->assertSame(4, $note->octave());
        $this->assertSame(Duration::QUARTER, $note->duration());
    }

    public function testOctaveOutOfRangeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Note(Pitch::C, 0, Duration::QUARTER);
    }

    public function testOctaveTooHighThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Note(Pitch::C, 9, Duration::QUARTER);
    }

    public function testEqualityWhenAllPropertiesMatch(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::C, 4, Duration::QUARTER);

        $this->assertTrue($a->equals($b));
    }

    public function testInequalityOnDifferentPitch(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::D, 4, Duration::QUARTER);

        $this->assertFalse($a->equals($b));
    }

    public function testInequalityOnDifferentOctave(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::C, 5, Duration::QUARTER);

        $this->assertFalse($a->equals($b));
    }

    public function testInequalityOnDifferentDuration(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::C, 4, Duration::HALF);

        $this->assertFalse($a->equals($b));
    }

    public function testMidiNumber(): void
    {
        $c4 = new Note(Pitch::C, 4, Duration::QUARTER);
        $this->assertSame(60, $c4->midiNumber());

        $a4 = new Note(Pitch::A, 4, Duration::QUARTER);
        $this->assertSame(69, $a4->midiNumber());
    }

    public function testImmutability(): void
    {
        $note = new Note(Pitch::C, 4, Duration::QUARTER);
        $newNote = $note->withDuration(Duration::HALF);

        $this->assertSame(Duration::QUARTER, $note->duration());
        $this->assertSame(Duration::HALF, $newNote->duration());
    }

    public function testToString(): void
    {
        $note = new Note(Pitch::C, 4, Duration::QUARTER);
        $this->assertSame('C4(quarter)', (string) $note);
    }
}
