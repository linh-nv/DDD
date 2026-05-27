<?php

namespace Testcenter\Application\Question\UseCase;

use Testcenter\Domain\Question\QuestionFactory;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionRepository;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\QuestionType;
use Testcenter\Domain\Shared\Score;

class CreateQuestionHandler
{
    public function __construct(
        private readonly QuestionRepository $questionRepository,
    ) {}

    public function handle(CreateQuestionCommand $command): QuestionResponse
    {
        $questionId = QuestionID::generate();

        $question = QuestionFactory::create(
            id: $questionId->value(),
            type: QuestionType::from($command->type),
            text: new QuestionText($command->content),
            score: new Score($command->score),
            payload: $command->payload,
        );

        $this->questionRepository->save($question);

        return new QuestionResponse($questionId->value());
    }
}
