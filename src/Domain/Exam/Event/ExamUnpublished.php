<?php

namespace Testcenter\Domain\Exam\Event;

use DateTimeImmutable;
use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Shared\DomainEvent;

class ExamUnpublished implements DomainEvent
{
    private readonly DateTimeImmutable $occurredOn;

    public function __construct(public readonly ExamID $examId)
    {
        $this->occurredOn = new DateTimeImmutable();
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
