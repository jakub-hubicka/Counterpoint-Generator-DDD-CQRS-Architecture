<?php

declare(strict_types=1);

namespace App\Composition\Domain\Rule;

use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Voice;

interface CounterpointRuleInterface
{
    /**
     * @throws \App\Composition\Domain\Exception\CounterpointRuleViolationException
     */
    public function validate(Voice $voice, CantusFirmus $cf): void;
}
