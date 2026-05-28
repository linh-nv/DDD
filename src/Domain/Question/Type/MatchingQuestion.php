<?php

namespace Testcenter\Domain\Question\Type;

use Testcenter\Domain\Question\Pair\MatchingPair;
use Testcenter\Domain\Question\Pair\MatchingPairs;
use Testcenter\Domain\Question\Question;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\QuestionType;
use Testcenter\Domain\Shared\Score;
use Testcenter\Domain\Submission\Answer\Answer;
use Testcenter\Domain\Submission\Answer\MatchingAnswer;
use Testcenter\Domain\Submission\GradeResult;

class MatchingQuestion extends Question
{
    public function __construct(
        QuestionID $id,
        QuestionText $text,
        Score $score,
        private MatchingPairs $pairs,
    ) {
        parent::__construct($id, QuestionType::MATCHING, $text, $score);
    }

    public function grade(Answer $answer): GradeResult
    {
        if (!$answer instanceof MatchingAnswer) {
            throw new \InvalidArgumentException('Invalid answer type');
        }

        $correct = 0;
        foreach ($answer->value() as $left => $right) {
            if ($this->pairs->isCorrect($left, $right)) {
                $correct++;
            }
        }

        if ($this->pairs->total() === 0) {
            return new GradeResult(true, $this->score());
        }

        return new GradeResult(
            ($correct / $this->pairs->total()) >= 0.5,
            new Score(($correct / $this->pairs->total()) * $this->score->value())
        );
    }

    public function createAnswer(mixed $userAnswer): Answer
    {
        return new MatchingAnswer($userAnswer);
    }

    public function updatePayload(array $payload): void
    {
        $raw = $payload['pairs'] ?? [];
        $this->pairs = new MatchingPairs(
            array_map(
                fn(string $left, string $right) => new MatchingPair($left, $right),
                array_keys($raw),
                array_values($raw),
            )
        );
    }

    public function toPayload(): array
    {
        $pairs = [];
        foreach ($this->pairs->all() as $pair) {
            $pairs[$pair->left()] = $pair->right();
        }
        return [
            'pairs'    => $pairs,
            '_summary' => implode(', ', array_map(
                fn($l, $r) => "{$l}→{$r}",
                array_keys($pairs),
                array_values($pairs)
            )),
        ];
    }
}