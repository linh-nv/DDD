<?php

namespace Testcenter\Application\Submission\UseCase;

class SubmitExamCommand
{
    public function __construct(
        public string $examId,
        public int $userId,
        public array $answers
    ) {}
}
