<?php

namespace Testcenter\Application\Exam\UseCase;

use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamQuestionRepository;

class ReorderExamQuestionsHandler
{
    public function __construct(
        private readonly ExamQuestionRepository $examQuestionRepository,
    ) {}

    public function handle(ReorderExamQuestionsCommand $command): void
    {
        $this->examQuestionRepository->reorder(
            new ExamID($command->examId),
            $command->questionSortOrders,
        );
    }
}
