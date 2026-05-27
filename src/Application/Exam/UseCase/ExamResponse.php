<?php

namespace Testcenter\Application\Exam\UseCase;

class ExamResponse
{
    public function __construct(public readonly string $examId) {}
}
