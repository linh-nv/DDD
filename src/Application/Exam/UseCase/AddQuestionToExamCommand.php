<?php

namespace Testcenter\Application\Exam\UseCase;

class AddQuestionToExamCommand
{
    public function __construct(
        public readonly string $examId,
        public readonly string $questionId,
    ) {}
}
