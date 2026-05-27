<?php

namespace Testcenter\Domain\Question;

use Testcenter\Domain\Question\Category\Categories;
use Testcenter\Domain\Question\Category\CategoryMap;
use Testcenter\Domain\Question\Exception\UnsupportedQuestionTypeException;
use Testcenter\Domain\Question\Pair\MatchingPair;
use Testcenter\Domain\Question\Pair\MatchingPairs;
use Testcenter\Domain\Question\Type\CategoryQuestion;
use Testcenter\Domain\Question\Type\FillBlankQuestion;
use Testcenter\Domain\Question\Type\MatchingQuestion;
use Testcenter\Domain\Question\Type\MultipleChoiceQuestion;
use Testcenter\Domain\Question\Type\OrderingQuestion;
use Testcenter\Domain\Question\Type\SingleChoiceQuestion;
use Testcenter\Domain\Question\Type\TrueFalseQuestion;
use Testcenter\Domain\Shared\Score;

class QuestionFactory
{
    /**
     * Create a domain Question from raw data.
     * Pass id = 0 for a new (not yet persisted) question.
     *
     * @throws UnsupportedQuestionTypeException
     */
    public static function create(
        string $id,
        QuestionType $type,
        QuestionText $text,
        Score $score,
        array $payload,
    ): Question {
        $qid = new QuestionID($id);

        return match ($type) {
            QuestionType::TRUE_FALSE => new TrueFalseQuestion(
                id: $qid,
                text: $text,
                score: $score,
                correct: (bool) ($payload['correct'] ?? false),
            ),

            QuestionType::SINGLE_CHOICE => new SingleChoiceQuestion(
                id: $qid,
                text: $text,
                score: $score,
                options: new OptionCollection($payload['options'] ?? []),
                correct: $payload['correct'] ?? '',
            ),

            QuestionType::MULTIPLE_CHOICE => new MultipleChoiceQuestion(
                id: $qid,
                text: $text,
                score: $score,
                options: new OptionCollection($payload['options'] ?? []),
                correct: $payload['correct'] ?? [],
            ),

            QuestionType::FILL_BLANK => new FillBlankQuestion(
                id: $qid,
                text: $text,
                score: $score,
                acceptedAnswers: new AcceptedAnswers($payload['answers'] ?? []),
            ),

            QuestionType::MATCHING => new MatchingQuestion(
                id: $qid,
                text: $text,
                score: $score,
                pairs: new MatchingPairs(
                    array_map(
                        fn(string $left, string $right) => new MatchingPair($left, $right),
                        array_keys($payload['pairs'] ?? []),
                        array_values($payload['pairs'] ?? []),
                    )
                ),
            ),

            QuestionType::ORDERING => new OrderingQuestion(
                id: $qid,
                text: $text,
                score: $score,
                correctOrder: $payload['correct_order'] ?? [],
            ),

            QuestionType::CATEGORY => new CategoryQuestion(
                id: $qid,
                text: $text,
                score: $score,
                categories: new Categories($payload['categories'] ?? []),
                correctMap: new CategoryMap(
                    $payload['correct_map'] ?? [],
                    new Categories($payload['categories'] ?? []),
                ),
            ),

            default => throw new UnsupportedQuestionTypeException(
                'Unsupported question type: ' . $type->value
            ),
        };
    }
}
