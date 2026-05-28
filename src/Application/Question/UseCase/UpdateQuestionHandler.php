<?php

namespace Testcenter\Application\Question\UseCase;

use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionRepository;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Shared\Score;

class UpdateQuestionHandler
{
    public function __construct(
        private readonly QuestionRepository $questionRepository,
    ) {}

    public function handle(UpdateQuestionCommand $command): void
    {
        $question = $this->questionRepository->findById(new QuestionID($command->questionId));

        $question->updateText(new QuestionText($command->content));
        $question->updateScore(new Score($command->score));
        $question->updatePayload($command->payload);

        $this->questionRepository->save($question);
    }
}
