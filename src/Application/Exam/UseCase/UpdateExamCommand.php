<?php

namespace Testcenter\Application\Exam\UseCase;

class UpdateExamCommand
{
    public function __construct(
        public readonly string $examId,
        public readonly string $title,
        public readonly string $description,
        public readonly int $durationMinutes,
    ) {}
}
