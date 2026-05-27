<?php

namespace Testcenter\Application\Question\UseCase;

use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionRepository;

class DeleteQuestionHandler
{
    public function __construct(
        private readonly QuestionRepository $questionRepository,
    ) {}

    public function handle(DeleteQuestionCommand $command): void
    {
        $questionId = new QuestionID($command->questionId);

        // Guard: verify exists
        $this->questionRepository->findById($questionId);

        $this->questionRepository->delete($questionId);
    }
}
