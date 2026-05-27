<?php

namespace Testcenter\Domain\Question;

use Testcenter\Domain\Shared\AggregateRoot;
use Testcenter\Domain\Shared\Score;
use Testcenter\Domain\Submission\Answer\Answer;
use Testcenter\Domain\Submission\GradeResult;

abstract class Question extends AggregateRoot
{
    public function __construct(
        protected readonly QuestionID $id,
        protected readonly QuestionType $type,
        protected QuestionText $text,
        protected Score $score,
    ) {
    }

    public function id(): QuestionID
    {
        return $this->id;
    }

    public function type(): QuestionType
    {
        return $this->type;
    }

    public function getText(): QuestionText
    {
        return $this->text;
    }

    public function text(): QuestionText
    {
        return $this->text;
    }

    public function updateText(QuestionText $text): void
    {
        $this->text = $text;
    }

    public function score(): Score
    {
        return $this->score;
    }

    public function updateScore(Score $score): void
    {
        $this->score = $score;
    }

    abstract public function grade(Answer $answer): GradeResult;
    abstract public function createAnswer(mixed $userAnswer): Answer;
    abstract public function toPayload(): array;
}