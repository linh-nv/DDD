<?php

namespace Testcenter\Domain\Exam;

class DurationMinutes
{
    public function __construct(private readonly int $minutes)
    {
        if ($minutes <= 0) {
            throw new \InvalidArgumentException('Duration must be greater than zero');
        }
    }

    public function value(): int
    {
        return $this->minutes;
    }
}
