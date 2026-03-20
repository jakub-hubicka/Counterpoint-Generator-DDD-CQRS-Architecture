<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\Model;

use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CantusFirmusTest extends TestCase
{
    public function testFirstNoteMustBeTonic(): void
    {
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);

        $this->expectException(\DomainException::class);
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));
    }

    public function testFirstNoteOnTonic(): void
    {
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));

        $this->assertSame(1, $cf->melody()->count());
    }

    public function testStepwiseMotionEnforced(): void
    {
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));

        // C to E is a third (not stepwise)
        $this->expectException(\DomainException::class);
        $cf->addNote(new Note(Pitch::E, 4, Duration::WHOLE));
    }

    public function testStepwiseMotionAllowed(): void
    {
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        $this->assertSame(2, $cf->melody()->count());
    }

    public function testIsCompleteWhenEndingOnTonic(): void
    {
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));

        $this->assertTrue($cf->isComplete());
    }

    public function testIsNotCompleteWhenNotEndingOnTonic(): void
    {
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        $this->assertFalse($cf->isComplete());
    }
}
