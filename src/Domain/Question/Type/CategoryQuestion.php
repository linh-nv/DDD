<?php

namespace Testcenter\Domain\Question\Type;

use Testcenter\Domain\Question\Category\Categories;
use Testcenter\Domain\Question\Category\CategoryMap;
use Testcenter\Domain\Question\Question;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\QuestionType;
use Testcenter\Domain\Shared\Score;
use Testcenter\Domain\Submission\Answer\Answer;
use Testcenter\Domain\Submission\Answer\CategoryAnswer;
use Testcenter\Domain\Submission\GradeResult;

class CategoryQuestion extends Question
{
    public function __construct(
        QuestionID $id,
        QuestionText $text,
        Score $score,
        private readonly Categories $categories,
        private readonly CategoryMap $correctMap,
    ) {
        parent::__construct($id, QuestionType::CATEGORY, $text, $score);
    }

    public function categories(): Categories
    {
        return $this->categories;
    }

    public function grade(Answer $answer): GradeResult
    {
        if (!$answer instanceof CategoryAnswer) {
            throw new \InvalidArgumentException('Invalid answer type');
        }

        $correct = 0;
        foreach ($answer->value() as $item => $category) {
            if ($this->correctMap->isCorrect($item, $category)) {
                $correct++;
            }
        }

        $total = $this->correctMap->total();

        if ($total === 0) {
            return new GradeResult(true, $this->score());
        }

        return new GradeResult(
            ($correct / $total) >= 0.5,
            new Score(($correct / $total) * $this->score->value())
        );
    }

    public function createAnswer(mixed $userAnswer): Answer
    {
        return new CategoryAnswer($userAnswer);
    }

    public function toPayload(): array
    {
        $correctMap = $this->correctMap->all();
        return [
            'categories'  => $this->categories->all(),
            'correct_map' => $correctMap,
            '_summary'    => implode(', ', array_map(
                fn($item, $cat) => "{$item}→{$cat}",
                array_keys($correctMap),
                array_values($correctMap)
            )),
        ];
    }
}
