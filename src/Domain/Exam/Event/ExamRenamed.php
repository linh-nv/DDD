<?php

namespace Testcenter\Domain\Exam\Event;

use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\Title;
use Testcenter\Domain\Shared\DomainEvent;

class ExamRenamed implements DomainEvent
{
    public function __construct(
        private readonly ExamID $examId,
        private readonly Title $newTitle,
        private readonly \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
    }

    public function examId(): ExamID
    {
        return $this->examId;
    }

    public function newTitle(): Title
    {
        return $this->newTitle;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
