<?php

namespace Testcenter\Domain\Submission;

use Testcenter\Domain\Exam\Exam;
use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\Exception\ExamNotActiveException;
use Testcenter\Domain\Shared\AggregateRoot;
use Testcenter\Domain\Submission\Answer\AnswerCollection;
use Testcenter\Domain\Submission\Exception\SubmissionException;
use Testcenter\Domain\User\UserID;

class Submission extends AggregateRoot
{
    private ?ScoreResult $scoreResult = null;

    public function __construct(
        private readonly SubmissionID $id,
        private readonly UserID $userId,
        private readonly ExamID $examId,
        private readonly AnswerCollection $answers
    ) {
    }

    public function id(): SubmissionID
    {
        return $this->id;
    }

    public function getUserId(): UserID
    {
        return $this->userId;
    }

    public function getExamId(): ExamID
    {
        return $this->examId;
    }

    public function getAnswers(): AnswerCollection
    {
        return $this->answers;
    }

    /**
     * @throws ExamNotActiveException
     */
    public static function submit(
        UserID $userId,
        Exam $exam,
        array $answers
    ): self {
        if (!$exam->isActive()) {
            throw new ExamNotActiveException('Exam is not active');
        }

        $submission = new self(
            id: SubmissionID::generate(),
            userId: $userId,
            examId: $exam->id(),
            answers: new AnswerCollection($answers)
        );

        $submission->recordEvent(new Event\ExamSubmitted($submission));

        return $submission;
    }

    public function applyScore(ScoreResult $scoreResult): void
    {
        $this->scoreResult = $scoreResult;
    }

    public function isScored(): bool
    {
        return $this->scoreResult !== null;
    }

    public function getScoreResult(): ScoreResult
    {
        if ($this->scoreResult === null) {
            throw new SubmissionException('Score result not available');
        }

        return $this->scoreResult;
    }
}
