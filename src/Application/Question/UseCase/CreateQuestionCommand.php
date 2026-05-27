<?php

namespace Testcenter\Application\Question\UseCase;

class CreateQuestionCommand
{
    public function __construct(
        public readonly string $type,
        public readonly string $content,
        public readonly int $score,
        public readonly array $payload,
        public readonly ?string $correctAnswerSummary = null,
    ) {}
}
