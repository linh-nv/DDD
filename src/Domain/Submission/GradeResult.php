<?php

namespace Testcenter\Domain\Submission;

use Testcenter\Domain\Shared\Score;

class GradeResult
{
    public function __construct(
        private readonly bool $correct,
        private readonly Score $earnedScore,
    ) {
    }

    public static function incorrect(): self
    {
        return new self(false, new Score(0));
    }

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function score(): Score
    {
        return $this->earnedScore;
    }
}