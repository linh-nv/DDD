<?php

namespace Testcenter\Application\Question\UseCase;

class QuestionResponse
{
    public function __construct(public readonly string $questionId) {}
}
