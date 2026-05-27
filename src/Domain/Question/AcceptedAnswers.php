<?php

namespace Testcenter\Domain\Question;

class AcceptedAnswers
{
    public function __construct(
        private readonly array $answers
    ) {
    }

    public function all(): array
    {
        return $this->answers;
    }

    public function contains(string $answer): bool
    {
        foreach ($this->answers as $accepted) {
            if (
                strtolower(trim($accepted)) ===
                strtolower(trim($answer))
            ) {
                return true;
            }
        }

        return false;
    }
}