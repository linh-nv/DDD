<?php

namespace Testcenter\Application\Exam\UseCase;

use Testcenter\Domain\Exam\Description;
use Testcenter\Domain\Exam\DurationMinutes;
use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamRepository;
use Testcenter\Domain\Exam\Title;
use Testcenter\Domain\Shared\DomainEventPublisher;

class UpdateExamHandler
{
    public function __construct(
        private readonly ExamRepository $examRepository,
        private readonly DomainEventPublisher $publisher,
    ) {}

    public function handle(UpdateExamCommand $command): void
    {
        $exam = $this->examRepository->findById(new ExamID($command->examId));

        $exam->rename(new Title($command->title));
        $exam->updateDescription(new Description($command->description));
        $exam->updateDuration(new DurationMinutes($command->durationMinutes));

        $this->examRepository->save($exam);
        $this->publisher->publish(...$exam->releaseEvents());
    }
}
