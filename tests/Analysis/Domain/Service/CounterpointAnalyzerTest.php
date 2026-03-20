<?php

declare(strict_types=1);

namespace App\Tests\Analysis\Domain\Service;

use App\Analysis\Domain\Service\CounterpointAnalyzer;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Rule\DissonanceResolutionRule;
use App\Composition\Domain\Rule\ParallelFifthsRule;
use App\Composition\Domain\Rule\ParallelOctavesRule;
use App\Composition\Domain\Rule\VoiceLeadingRule;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use App\Composition\Domain\ValueObject\VoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CounterpointAnalyzerTest extends TestCase
{
    public function testAnalyzeWithNoViolations(): void
    {
        $analyzer = new CounterpointAnalyzer([
            new ParallelFifthsRule(),
            new ParallelOctavesRule(),
            new VoiceLeadingRule(),
            new DissonanceResolutionRule(),
        ]);

        // CF: C4 -> D4
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        // Voice: E4 -> F4 (consonant thirds, stepwise, no parallels)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::E, 4, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::F, 4, Duration::WHOLE));

        $result = $analyzer->analyze('comp-1', $voice, $cf);

        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->violations());
    }

    public function testAnalyzeDetectsViolations(): void
    {
        $analyzer = new CounterpointAnalyzer([
            new ParallelFifthsRule(),
        ]);

        // CF: C4 -> D4
        $cf = new CantusFirmus(Uuid::v7(), 'comp-1', Pitch::C);
        $cf->addNote(new Note(Pitch::C, 4, Duration::WHOLE));
        $cf->addNote(new Note(Pitch::D, 4, Duration::WHOLE));

        // Voice: G4 -> A4 (parallel fifths)
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);
        $voice->addNote(new Note(Pitch::G, 4, Duration::WHOLE));
        $voice->addNote(new Note(Pitch::A, 4, Duration::WHOLE));

        $result = $analyzer->analyze('comp-1', $voice, $cf);

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->violations());
        $this->assertSame('ParallelFifths', $result->violations()[0]['rule']);
    }
}
