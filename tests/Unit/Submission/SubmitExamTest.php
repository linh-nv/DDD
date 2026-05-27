<?php

namespace Tests\Unit\Submission;

use PHPUnit\Framework\TestCase;
use Testcenter\Domain\Exam\Description;
use Testcenter\Domain\Exam\Exam;
use Testcenter\Domain\Exam\DurationMinutes;
use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamStatus;
use Testcenter\Domain\Exam\Exception\ExamNotActiveException;
use Testcenter\Domain\Exam\Title;
use Testcenter\Domain\Question\OptionCollection;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\Type\SingleChoiceQuestion;
use Testcenter\Domain\Shared\Score;
use Testcenter\Domain\Submission\Answer\SingleChoiceAnswer;
use Testcenter\Domain\Submission\Event\ExamSubmitted;
use Testcenter\Domain\Submission\Exception\SubmissionException;
use Testcenter\Domain\Submission\GradeResult;
use Testcenter\Domain\Submission\ScoreResult;
use Testcenter\Domain\Submission\Submission;
use Testcenter\Domain\User\UserID;

class SubmitExamTest extends TestCase
{
    private const EXAM_ID = 'b0000000-0000-0000-0000-000000000001';
    private const Q1_ID   = 'a0000000-0000-0000-0000-000000000001';

    private function makeActiveExam(): Exam
    {
        return new Exam(
            id: new ExamID(self::EXAM_ID),
            examStatus: ExamStatus::ACTIVE,
            title: new Title('PHP Fundamentals'),
            description: new Description('Test your PHP knowledge'),
            durationMinutes: new DurationMinutes(60),
        );
    }

    private function makeInactiveExam(): Exam
    {
        return new Exam(
            id: new ExamID(self::EXAM_ID),
            examStatus: ExamStatus::INACTIVE,
            title: new Title('PHP Fundamentals'),
            description: new Description('Test your PHP knowledge'),
            durationMinutes: new DurationMinutes(60),
        );
    }

    private function makeAnswers(): array
    {
        $question = new SingleChoiceQuestion(
            id: new QuestionID(self::Q1_ID),
            text: new QuestionText('What does PHP stand for?'),
            score: new Score(10),
            options: new OptionCollection(['A' => 'PHP: Hypertext Preprocessor', 'B' => 'Personal Home Page']),
            correct: 'A',
        );

        return [self::Q1_ID => $question->createAnswer('A')];
    }

    public function test_submit_creates_submission_for_active_exam(): void
    {
        $submission = Submission::submit(
            userId: new UserID(42),
            exam: $this->makeActiveExam(),
            answers: $this->makeAnswers(),
        );

        $this->assertInstanceOf(Submission::class, $submission);
        $this->assertEquals(42, $submission->getUserId()->value());
        $this->assertEquals(self::EXAM_ID, $submission->getExamId()->value());
    }

    public function test_submit_assigns_a_uuid_id(): void
    {
        $submission = Submission::submit(
            userId: new UserID(42),
            exam: $this->makeActiveExam(),
            answers: $this->makeAnswers(),
        );

        $this->assertNotEmpty($submission->id()->value());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $submission->id()->value()
        );
    }

    public function test_submit_throws_when_exam_is_inactive(): void
    {
        $this->expectException(ExamNotActiveException::class);

        Submission::submit(
            userId: new UserID(42),
            exam: $this->makeInactiveExam(),
            answers: $this->makeAnswers(),
        );
    }

    public function test_submit_records_exam_submitted_event(): void
    {
        $submission = Submission::submit(
            userId: new UserID(42),
            exam: $this->makeActiveExam(),
            answers: $this->makeAnswers(),
        );

        $events = $submission->releaseEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(ExamSubmitted::class, $events[0]);
    }

    public function test_release_events_clears_the_event_list(): void
    {
        $submission = Submission::submit(
            userId: new UserID(42),
            exam: $this->makeActiveExam(),
            answers: $this->makeAnswers(),
        );

        $submission->releaseEvents();

        $this->assertCount(0, $submission->releaseEvents());
    }

    public function test_is_scored_returns_false_before_score_is_applied(): void
    {
        $submission = Submission::submit(
            userId: new UserID(42),
            exam: $this->makeActiveExam(),
            answers: $this->makeAnswers(),
        );

        $this->assertFalse($submission->isScored());
    }

    public function test_is_scored_returns_true_after_score_is_applied(): void
    {
        $submission = Submission::submit(
            userId: new UserID(42),
            exam: $this->makeActiveExam(),
            answers: $this->makeAnswers(),
        );

        $submission->applyScore(new ScoreResult(10, [
            self::Q1_ID => new GradeResult(true, new Score(10)),
        ]));

        $this->assertTrue($submission->isScored());
    }

    public function test_get_score_result_returns_applied_score(): void
    {
        $submission = Submission::submit(
            userId: new UserID(42),
            exam: $this->makeActiveExam(),
            answers: $this->makeAnswers(),
        );

        $scoreResult = new ScoreResult(10, [
            self::Q1_ID => new GradeResult(true, new Score(10)),
        ]);
        $submission->applyScore($scoreResult);

        $this->assertSame($scoreResult, $submission->getScoreResult());
        $this->assertEquals(10, $submission->getScoreResult()->total());
    }

    public function test_get_score_result_throws_when_not_scored(): void
    {
        $submission = Submission::submit(
            userId: new UserID(42),
            exam: $this->makeActiveExam(),
            answers: $this->makeAnswers(),
        );

        $this->expectException(SubmissionException::class);

        $submission->getScoreResult();
    }
}
