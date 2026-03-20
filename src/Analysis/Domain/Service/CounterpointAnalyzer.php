<?php

declare(strict_types=1);

namespace App\Analysis\Domain\Service;

use App\Analysis\Domain\ValueObject\AnalysisResult;
use App\Composition\Domain\Exception\CounterpointRuleViolationException;
use App\Composition\Domain\Model\CantusFirmus;
use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Rule\CounterpointRuleInterface;

final class CounterpointAnalyzer
{
    /** @var iterable<CounterpointRuleInterface> */
    private iterable $rules;

    /** @param iterable<CounterpointRuleInterface> $rules */
    public function __construct(iterable $rules)
    {
        $this->rules = $rules;
    }

    public function analyze(string $compositionId, Voice $voice, CantusFirmus $cf): AnalysisResult
    {
        $violations = [];

        foreach ($this->rules as $rule) {
            try {
                $rule->validate($voice, $cf);
            } catch (CounterpointRuleViolationException $e) {
                $violations[] = [
                    'rule' => $e->ruleName(),
                    'message' => $e->getMessage(),
                    'measureIndex' => $e->measureIndex(),
                ];
            }
        }

        return new AnalysisResult(
            compositionId: $compositionId,
            violations: $violations,
            valid: count($violations) === 0,
        );
    }
}
