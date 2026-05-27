<?php

namespace Testcenter\Application\Question\UseCase;

class DeleteQuestionCommand
{
    public function __construct(public readonly string $questionId) {}
}
