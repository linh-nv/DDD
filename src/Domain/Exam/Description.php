<?php

namespace Testcenter\Domain\Exam;

class Description
{
    public function __construct(private readonly string $description) {}

    public function value(): string
    {
        return $this->description;
    }
}