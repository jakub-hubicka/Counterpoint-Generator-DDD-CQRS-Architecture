<?php

declare(strict_types=1);

namespace App\Composition\Domain\Rule;

use App\Composition\Domain\Exception\CounterpointRuleViolationException;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Interval;
use App\Composition\Domain\Model\Voice;

final class ParallelOctavesRule implements CounterpointRuleInterface
{
    public function validate(Voice $voice, CantusFirmus $cf): void
    {
        $voiceNotes = $voice->melody()->notes();
        $cfNotes = $cf->melody()->notes();
        $length = min(count($voiceNotes), count($cfNotes));

        for ($i = 1; $i < $length; $i++) {
            $prevInterval = new Interval($cfNotes[$i - 1], $voiceNotes[$i - 1]);
            $currInterval = new Interval($cfNotes[$i], $voiceNotes[$i]);

            $isPrevOctaveOrUnison = in_array($prevInterval->diatonicSteps(), [0, 7], true);
            $isCurrOctaveOrUnison = in_array($currInterval->diatonicSteps(), [0, 7], true);

            if ($isPrevOctaveOrUnison && $isCurrOctaveOrUnison) {
                throw new CounterpointRuleViolationException(
                    'ParallelOctaves',
                    sprintf('Parallel octaves/unisons detected between positions %d and %d', $i - 1, $i),
                    $i
                );
            }
        }
    }
}
