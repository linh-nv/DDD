<?php

namespace Testcenter\Application\Exam\UseCase;

use Testcenter\Domain\Exam\Description;
use Testcenter\Domain\Exam\DurationMinutes;
use Testcenter\Domain\Exam\ExamRepository;
use Testcenter\Domain\Exam\ExamStatus;
use Testcenter\Domain\Exam\Title;

class CreateExamHandler
{
    public function __construct(
        private readonly ExamRepository $examRepository,
    ) {}

    public function handle(CreateExamCommand $command): ExamResponse
    {
        $title           = new Title($command->title);
        $description     = new Description($command->description);
        $durationMinutes = new DurationMinutes($command->durationMinutes);
        $status          = $command->isActive ? ExamStatus::ACTIVE : ExamStatus::INACTIVE;

        $examId = $this->examRepository->create($title, $description, $durationMinutes, $status);

        return new ExamResponse($examId->value());
    }
}
