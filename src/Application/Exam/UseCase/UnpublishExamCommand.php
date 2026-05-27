<?php

namespace Testcenter\Application\Exam\UseCase;

class UnpublishExamCommand
{
    public function __construct(public readonly string $examId) {}
}
