<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\Rule;

use App\Composition\Domain\Exception\CounterpointRuleViolationException;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Rule\ParallelOctavesRule;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use App\Composition\Domain\ValueObject\VoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class ParallelOctavesRuleTest extends TestCase
{
    private ParallelOctavesRule $rule;

    protected function setUp(): void
    {
        $this->rule = new ParallelOctavesRule();
    }

    public function testDetectsParallelOctaves(): void
    {
        // CF: C4 -> D4
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        // Voice: C5 -> D5 (octaves above CF)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::C, 5, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::D, 5, Duration::WHOLE));

        $this->expectException(CounterpointRuleViolationException::class);
        $this->rule->validate($voice, $cf);
    }

    public function testNoParallelOctavesPasses(): void
    {
        // CF: C4 -> D4
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        // Voice: C5 -> F4 (octave then third — not parallel)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::C, 5, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::F, 4, Duration::WHOLE));

        $this->rule->validate($voice, $cf);
        $this->addToAssertionCount(1);
    }
}
