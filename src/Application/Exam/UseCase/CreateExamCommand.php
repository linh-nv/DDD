<?php

namespace Testcenter\Application\Exam\UseCase;

class CreateExamCommand
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly int $durationMinutes,
        public readonly bool $isActive = false,
    ) {}
}
