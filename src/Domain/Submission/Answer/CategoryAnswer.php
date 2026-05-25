<?php

namespace Testcenter\Domain\Submission\Answer;

class CategoryAnswer implements Answer
{
    /**
     * @param array<string, string> $assignments item => category
     */
    public function __construct(
        private readonly array $assignments,
    ) {
    }

    public function value(): array
    {
        return $this->assignments;
    }
}
