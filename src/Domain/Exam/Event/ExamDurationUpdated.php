<?php

namespace Testcenter\Domain\Exam\Event;

use DateTimeImmutable;
use Testcenter\Domain\Exam\DurationMinutes;
use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Shared\DomainEvent;

class ExamDurationUpdated implements DomainEvent
{
    private readonly DateTimeImmutable $occurredOn;

    public function __construct(
        public readonly ExamID $examId,
        public readonly DurationMinutes $newDuration,
    ) {
        $this->occurredOn = new DateTimeImmutable();
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
