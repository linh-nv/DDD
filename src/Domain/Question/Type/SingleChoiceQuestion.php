<?php

namespace Testcenter\Domain\Question\Type;

use Testcenter\Domain\Question\OptionCollection;
use Testcenter\Domain\Question\Question;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\QuestionType;
use Testcenter\Domain\Shared\Score;
use Testcenter\Domain\Submission\Answer\Answer;
use Testcenter\Domain\Submission\Answer\SingleChoiceAnswer;
use Testcenter\Domain\Submission\GradeResult;

class SingleChoiceQuestion extends Question
{
    public function __construct(
        QuestionID $id,
        QuestionText $text,
        Score $score,
        private OptionCollection $options,
        private string $correct,
    ) {
        parent::__construct($id, QuestionType::SINGLE_CHOICE, $text, $score);
    }

    public function grade(Answer $answer): GradeResult
    {
        if (!$answer instanceof SingleChoiceAnswer) {
            throw new \InvalidArgumentException('Invalid answer type');
        }

        return $answer->value() === $this->correct
            ? new GradeResult(true, $this->score())
            : GradeResult::incorrect();
    }

    public function options(): OptionCollection
    {
        return $this->options;
    }

    public function createAnswer(mixed $userAnswer): Answer
    {
        return new SingleChoiceAnswer($userAnswer);
    }

    public function updatePayload(array $payload): void
    {
        $this->options = new OptionCollection($payload['options'] ?? []);
        $this->correct = $payload['correct'] ?? '';
    }

    public function toPayload(): array
    {
        return [
            'options' => $this->options->options,
            'correct' => $this->correct,
            '_summary' => $this->correct,
        ];
    }
}