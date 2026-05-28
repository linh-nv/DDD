<?php

namespace Testcenter\Domain\Question\Type;

use Testcenter\Domain\Question\OptionCollection;
use Testcenter\Domain\Question\Question;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\QuestionType;
use Testcenter\Domain\Shared\Score;
use Testcenter\Domain\Submission\Answer\Answer;
use Testcenter\Domain\Submission\Answer\MultipleChoiceAnswer;
use Testcenter\Domain\Submission\GradeResult;

class MultipleChoiceQuestion extends Question
{
    public function __construct(
        QuestionID $id,
        QuestionText $text,
        Score $score,
        private OptionCollection $options,
        private array $correct,
    ) {
        if (empty($correct)) {
            throw new \InvalidArgumentException('Correct answers cannot be empty');
        }

        parent::__construct($id, QuestionType::MULTIPLE_CHOICE, $text, $score);
    }

    public function grade(Answer $answer): GradeResult
    {
        if (!$answer instanceof MultipleChoiceAnswer) {
            throw new \InvalidArgumentException('Invalid answer type');
        }

        return $this->normalize($answer->value()) === $this->normalize($this->correct)
            ? new GradeResult(true, $this->score())
            : GradeResult::incorrect();
    }

    public function options(): OptionCollection
    {
        return $this->options;
    }

    private function normalize(array $answers): array
    {
        $answers = array_values(array_unique($answers));
        sort($answers);

        return $answers;
    }

    public function createAnswer(mixed $userAnswer): Answer
    {
        return new MultipleChoiceAnswer($userAnswer);
    }

    public function updatePayload(array $payload): void
    {
        $correct = $payload['correct'] ?? [];
        if (empty($correct)) {
            throw new \InvalidArgumentException('Correct answers cannot be empty');
        }
        $this->options = new OptionCollection($payload['options'] ?? []);
        $this->correct = $correct;
    }

    public function toPayload(): array
    {
        return [
            'options'  => $this->options->options,
            'correct'  => $this->correct,
            '_summary' => implode(', ', $this->correct),
        ];
    }
}
