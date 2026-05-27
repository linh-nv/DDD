<?php

namespace Testcenter\Application\Exam\UseCase;

class DeleteExamCommand
{
    public function __construct(public readonly string $examId) {}
}
