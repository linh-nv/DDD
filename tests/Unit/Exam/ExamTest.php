<?php

namespace Tests\Unit\Exam;

use PHPUnit\Framework\TestCase;
use Testcenter\Domain\Exam\Description;
use Testcenter\Domain\Exam\Exam;
use Testcenter\Domain\Exam\Event\ExamDescriptionUpdated;
use Testcenter\Domain\Exam\Event\ExamPublished;
use Testcenter\Domain\Exam\Event\ExamRenamed;
use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamStatus;
use Testcenter\Domain\Exam\Exception\ExamCannotPublishException;
use Testcenter\Domain\Exam\Title;

class ExamTest extends TestCase
{
    private const EXAM_ID = 'b0000000-0000-0000-0000-000000000001';

    private function makeInactiveExam(): Exam
    {
        return new Exam(
            id: new ExamID(self::EXAM_ID),
            examStatus: ExamStatus::INACTIVE,
            title: new Title('PHP Basics'),
            description: new Description('Introduction to PHP'),
            durationMinutes: new \Testcenter\Domain\Exam\DurationMinutes(60),
        );
    }

    public function test_publish_activates_an_inactive_exam(): void
    {
        $exam = $this->makeInactiveExam();

        $exam->publish();

        $this->assertTrue($exam->isActive());
    }

    public function test_publish_records_exam_published_event(): void
    {
        $exam = $this->makeInactiveExam();

        $exam->publish();
        $events = $exam->releaseEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(ExamPublished::class, $events[0]);
        $this->assertEquals(self::EXAM_ID, $events[0]->examId()->value());
    }

    public function test_publish_throws_when_already_active(): void
    {
        $exam = new Exam(
            id: new ExamID(self::EXAM_ID),
            examStatus: ExamStatus::ACTIVE,
            title: new Title('PHP Basics'),
            description: new Description('Introduction to PHP'),
            durationMinutes: new \Testcenter\Domain\Exam\DurationMinutes(60),
        );

        $this->expectException(ExamCannotPublishException::class);

        $exam->publish();
    }

    public function test_rename_updates_title_and_records_event(): void
    {
        $exam = $this->makeInactiveExam();

        $exam->rename(new Title('Advanced PHP'));
        $events = $exam->releaseEvents();

        $this->assertEquals('Advanced PHP', $exam->getTitle()->value());
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ExamRenamed::class, $events[0]);
        $this->assertEquals('Advanced PHP', $events[0]->newTitle()->value());
    }

    public function test_rename_does_not_record_event_when_title_unchanged(): void
    {
        $exam = $this->makeInactiveExam();

        $exam->rename(new Title('PHP Basics'));
        $events = $exam->releaseEvents();

        $this->assertCount(0, $events);
    }

    public function test_update_description_records_event(): void
    {
        $exam = $this->makeInactiveExam();

        $exam->updateDescription(new Description('A deeper dive into PHP'));
        $events = $exam->releaseEvents();

        $this->assertEquals('A deeper dive into PHP', $exam->getDescription()->value());
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ExamDescriptionUpdated::class, $events[0]);
        $this->assertEquals('A deeper dive into PHP', $events[0]->newDescription()->value());
    }
}
