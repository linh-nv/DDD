<?php

namespace Testcenter\Application\Exam\UseCase;

class RemoveQuestionFromExamCommand
{
    public function __construct(
        public readonly string $examId,
        public readonly string $questionId,
    ) {}
}
