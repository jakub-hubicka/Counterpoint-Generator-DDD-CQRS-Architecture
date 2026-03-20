<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\Rule;

use App\Composition\Domain\Exception\CounterpointRuleViolationException;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Rule\VoiceLeadingRule;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use App\Composition\Domain\ValueObject\VoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class VoiceLeadingRuleTest extends TestCase
{
    private VoiceLeadingRule $rule;

    protected function setUp(): void
    {
        $this->rule = new VoiceLeadingRule();
    }

    public function testRejectsLeapLargerThanSixth(): void
    {
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        // Voice: C4 -> B4 (seventh — too large)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::B, 4, Duration::WHOLE));

        $this->expectException(CounterpointRuleViolationException::class);
        $this->rule->validate($voice, $cf);
    }

    public function testAllowsSixthOrSmaller(): void
    {
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        // Voice: C4 -> A4 (sixth — allowed)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::A, 4, Duration::WHOLE));

        $this->rule->validate($voice, $cf);
        $this->addToAssertionCount(1);
    }
}
