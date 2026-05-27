<?php

namespace Testcenter\Application\Exam\UseCase;

class PublishExamCommand
{
    public function __construct(public readonly string $examId) {}
}
