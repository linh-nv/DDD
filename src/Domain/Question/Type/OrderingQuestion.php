<?php

namespace Testcenter\Domain\Question\Type;

use Testcenter\Domain\Question\Question;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\QuestionType;
use Testcenter\Domain\Shared\Score;
use Testcenter\Domain\Submission\Answer\Answer;
use Testcenter\Domain\Submission\Answer\OrderingAnswer;
use Testcenter\Domain\Submission\GradeResult;

class OrderingQuestion extends Question
{
    public function __construct(
        QuestionID $id,
        QuestionText $text,
        Score $score,
        private array $correctOrder,
    ) {
        if (empty($correctOrder)) {
            throw new \InvalidArgumentException('Correct order cannot be empty');
        }

        parent::__construct($id, QuestionType::ORDERING, $text, $score);
    }

    public function grade(Answer $answer): GradeResult
    {
        if (!$answer instanceof OrderingAnswer) {
            throw new \InvalidArgumentException('Invalid answer type');
        }

        $correct = true;
        foreach (array_values($answer->value()) as $index => $item) {
            if (!isset($this->correctOrder[$index]) || $this->correctOrder[$index] !== $item) {
                $correct = false;
            }
        }

        return $correct ? new GradeResult(true, $this->score()) : GradeResult::incorrect();
    }

    public function correctOrder(): array
    {
        return $this->correctOrder;
    }

    public function createAnswer(mixed $userAnswer): Answer
    {
        return new OrderingAnswer($userAnswer);
    }

    public function updatePayload(array $payload): void
    {
        $correctOrder = $payload['correct_order'] ?? [];
        if (empty($correctOrder)) {
            throw new \InvalidArgumentException('Correct order cannot be empty');
        }
        $this->correctOrder = $correctOrder;
    }

    public function toPayload(): array
    {
        return [
            'correct_order' => $this->correctOrder,
            '_summary'      => implode(', ', $this->correctOrder),
        ];
    }
}
