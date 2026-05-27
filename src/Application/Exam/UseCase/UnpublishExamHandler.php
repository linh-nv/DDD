<?php

namespace Testcenter\Application\Exam\UseCase;

use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamRepository;
use Testcenter\Domain\Shared\DomainEventPublisher;

class UnpublishExamHandler
{
    public function __construct(
        private readonly ExamRepository $examRepository,
        private readonly DomainEventPublisher $publisher,
    ) {}

    public function handle(UnpublishExamCommand $command): void
    {
        $exam = $this->examRepository->findById(new ExamID($command->examId));

        $exam->unpublish();
        $this->examRepository->save($exam);
        $this->publisher->publish(...$exam->releaseEvents());
    }
}
