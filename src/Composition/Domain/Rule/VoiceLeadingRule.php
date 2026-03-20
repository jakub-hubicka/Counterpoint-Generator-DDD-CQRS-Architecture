<?php

declare(strict_types=1);

namespace App\Composition\Domain\Rule;

use App\Composition\Domain\Exception\CounterpointRuleViolationException;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Interval;
use App\Composition\Domain\Model\Voice;

final class VoiceLeadingRule implements CounterpointRuleInterface
{
    public function validate(Voice $voice, CantusFirmus $cf): void
    {
        $voiceNotes = $voice->melody()->notes();

        for ($i = 1; $i < count($voiceNotes); $i++) {
            $interval = new Interval($voiceNotes[$i - 1], $voiceNotes[$i]);

            if ($interval->isLargerThanSixth()) {
                throw new CounterpointRuleViolationException(
                    'VoiceLeading',
                    sprintf(
                        'Leap larger than a sixth detected at position %d (interval: %s)',
                        $i,
                        $interval->name()
                    ),
                    $i
                );
            }
        }
    }
}
