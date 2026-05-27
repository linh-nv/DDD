<?php

namespace Testcenter\Domain\Exam;

use Testcenter\Domain\Exam\Event\ExamDescriptionUpdated;
use Testcenter\Domain\Exam\Event\ExamDurationUpdated;
use Testcenter\Domain\Exam\Event\ExamPublished;
use Testcenter\Domain\Exam\Event\ExamRenamed;
use Testcenter\Domain\Exam\Event\ExamUnpublished;
use Testcenter\Domain\Exam\Exception\ExamCannotPublishException;
use Testcenter\Domain\Shared\AggregateRoot;

class Exam extends AggregateRoot
{
    public function __construct(
        private readonly ExamID $id,
        private ExamStatus $examStatus,
        private Title $title,
        private Description $description,
        private DurationMinutes $durationMinutes,
    ) {
    }

    public function id(): ExamID
    {
        return $this->id;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function getDurationMinutes(): DurationMinutes
    {
        return $this->durationMinutes;
    }

    public function isActive(): bool
    {
        return $this->examStatus === ExamStatus::ACTIVE;
    }

    public function rename(Title $newTitle): void
    {
        if ($this->title->value() === $newTitle->value()) {
            return;
        }
        $this->title = $newTitle;
        $this->recordEvent(new ExamRenamed($this->id, $newTitle));
    }

    public function updateDescription(Description $description): void
    {
        $this->description = $description;
        $this->recordEvent(new ExamDescriptionUpdated($this->id, $description));
    }

    public function updateDuration(DurationMinutes $durationMinutes): void
    {
        if ($this->durationMinutes->value() === $durationMinutes->value()) {
            return;
        }
        $this->durationMinutes = $durationMinutes;
        $this->recordEvent(new ExamDurationUpdated($this->id, $durationMinutes));
    }

    /**
     * @throws ExamCannotPublishException
     */
    public function publish(): void
    {
        if ($this->examStatus === ExamStatus::ACTIVE) {
            throw new ExamCannotPublishException('Exam is already published');
        }
        $this->examStatus = ExamStatus::ACTIVE;
        $this->recordEvent(new ExamPublished($this->id));
    }

    public function unpublish(): void
    {
        if ($this->examStatus === ExamStatus::INACTIVE) {
            return;
        }
        $this->examStatus = ExamStatus::INACTIVE;
        $this->recordEvent(new ExamUnpublished($this->id));
    }
}
