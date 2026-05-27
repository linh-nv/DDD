<?php

namespace Testcenter\Application\Exam\UseCase;

use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamQuestionRepository;
use Testcenter\Domain\Exam\ExamRepository;

class DeleteExamHandler
{
    public function __construct(
        private readonly ExamRepository $examRepository,
        private readonly ExamQuestionRepository $examQuestionRepository,
    ) {}

    public function handle(DeleteExamCommand $command): void
    {
        $examId = new ExamID($command->examId);

        // Verify exam exists before deleting
        $this->examRepository->findById($examId);

        $this->examQuestionRepository->removeAll($examId);
        $this->examRepository->delete($examId);
    }
}
