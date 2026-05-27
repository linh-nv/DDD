<?php

namespace Testcenter\Application\Exam\UseCase;

use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamQuestionRepository;
use Testcenter\Domain\Question\QuestionID;

class RemoveQuestionFromExamHandler
{
    public function __construct(
        private readonly ExamQuestionRepository $examQuestionRepository,
    ) {}

    public function handle(RemoveQuestionFromExamCommand $command): void
    {
        $this->examQuestionRepository->remove(
            new ExamID($command->examId),
            new QuestionID($command->questionId),
        );
    }
}
