<?php

declare(strict_types=1);

namespace App\Composition\Domain\Rule;

use App\Composition\Domain\Exception\CounterpointRuleViolationException;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Interval;
use App\Composition\Domain\Model\Voice;

final class DissonanceResolutionRule implements CounterpointRuleInterface
{
    public function validate(Voice $voice, CantusFirmus $cf): void
    {
        $voiceNotes = $voice->melody()->notes();
        $cfNotes = $cf->melody()->notes();
        $length = min(count($voiceNotes), count($cfNotes));

        for ($i = 0; $i < $length; $i++) {
            $verticalInterval = new Interval($cfNotes[$i], $voiceNotes[$i]);

            if ($verticalInterval->isDissonant()) {
                // Dissonances must resolve by stepwise motion in the voice
                if ($i + 1 >= count($voiceNotes)) {
                    throw new CounterpointRuleViolationException(
                        'DissonanceResolution',
                        sprintf('Unresolved dissonance at position %d', $i),
                        $i
                    );
                }

                $resolution = new Interval($voiceNotes[$i], $voiceNotes[$i + 1]);
                if (!$resolution->isStep()) {
                    throw new CounterpointRuleViolationException(
                        'DissonanceResolution',
                        sprintf(
                            'Dissonance at position %d must resolve by stepwise motion, got %s',
                            $i,
                            $resolution->name()
                        ),
                        $i
                    );
                }
            }
        }
    }
}
