<?php

namespace Testcenter\Domain\Exam\Event;

use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Shared\DomainEvent;

class ExamPublished implements DomainEvent
{
    public function __construct(
        private readonly ExamID $examId,
        private readonly \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
    }

    public function examId(): ExamID
    {
        return $this->examId;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
