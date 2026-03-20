<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\Rule;

use App\Composition\Domain\Exception\CounterpointRuleViolationException;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Rule\DissonanceResolutionRule;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use App\Composition\Domain\ValueObject\VoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class DissonanceResolutionRuleTest extends TestCase
{
    private DissonanceResolutionRule $rule;

    protected function setUp(): void
    {
        $this->rule = new DissonanceResolutionRule();
    }

    public function testDissonanceResolvedByStepPasses(): void
    {
        // CF: C4 -> D4 -> E4
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::E, 4, Duration::WHOLE));

        // Voice: E4 -> C5 -> B4
        // pos 0: C4 vs E4 = third (consonant)
        // pos 1: D4 vs C5 = seventh (dissonant), resolves C5->B4 by step
        // pos 2: E4 vs B4 = fifth (consonant)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::E, 4, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::C, 5, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::B, 4, Duration::WHOLE));

        $this->rule->validate($voice, $cf);
        $this->addToAssertionCount(1);
    }

    public function testUnresolvedDissonanceThrows(): void
    {
        // CF: C4 (single note)
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));

        // Voice: D4 (second with CF = dissonant, no next note to resolve)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        $this->expectException(CounterpointRuleViolationException::class);
        $this->rule->validate($voice, $cf);
    }

    public function testDissonanceResolvedByLeapThrows(): void
    {
        // CF: C4 -> D4 -> E4
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::E, 4, Duration::WHOLE));

        // Voice: D4 -> G4 (second with CF at pos 0 = dissonant, resolves by leap to G4)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::D, 4, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::G, 4, Duration::WHOLE));

        $this->expectException(CounterpointRuleViolationException::class);
        $this->rule->validate($voice, $cf);
    }

    public function testConsonantIntervalsPass(): void
    {
        // CF: C4 -> D4
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        // Voice: E4 -> F4 (third with CF = consonant)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::E, 4, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::F, 4, Duration::WHOLE));

        $this->rule->validate($voice, $cf);
        $this->addToAssertionCount(1);
    }
}
