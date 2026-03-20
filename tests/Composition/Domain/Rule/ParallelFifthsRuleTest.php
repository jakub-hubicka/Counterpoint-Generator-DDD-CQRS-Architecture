<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\Rule;

use App\Composition\Domain\Exception\CounterpointRuleViolationException;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Rule\ParallelFifthsRule;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use App\Composition\Domain\ValueObject\VoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class ParallelFifthsRuleTest extends TestCase
{
    private ParallelFifthsRule $rule;

    protected function setUp(): void
    {
        $this->rule = new ParallelFifthsRule();
    }

    public function testDetectsParallelFifths(): void
    {
        // CF: C4 -> D4 (stepwise)
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        // Voice: G4 -> A4 (both fifths above CF)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::G, 4, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::A, 4, Duration::WHOLE));

        $this->expectException(CounterpointRuleViolationException::class);
        $this->rule->validate($voice, $cf);
    }

    public function testNoParallelFifthsPasses(): void
    {
        // CF: C4 -> D4
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        // Voice: E4 -> F4 (thirds, not fifths)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::E, 4, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::F, 4, Duration::WHOLE));

        $this->rule->validate($voice, $cf);
        $this->addToAssertionCount(1);
    }
}
