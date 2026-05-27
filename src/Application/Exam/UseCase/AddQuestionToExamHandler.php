<?php

namespace Testcenter\Application\Exam\UseCase;

use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamQuestionRepository;
use Testcenter\Domain\Exam\ExamRepository;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionRepository;

class AddQuestionToExamHandler
{
    public function __construct(
        private readonly ExamRepository $examRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly ExamQuestionRepository $examQuestionRepository,
    ) {}

    public function handle(AddQuestionToExamCommand $command): void
    {
        $examId     = new ExamID($command->examId);
        $questionId = new QuestionID($command->questionId);

        // Guard: both must exist
        $this->examRepository->findById($examId);
        $this->questionRepository->findById($questionId);

        if ($this->examQuestionRepository->exists($examId, $questionId)) {
            return;
        }

        $nextOrder = $this->examQuestionRepository->maxSortOrder($examId) + 1;
        $this->examQuestionRepository->add($examId, $questionId, $nextOrder);
    }
}
