<?php

namespace Testcenter\Infrastructure\Question;

use Testcenter\Domain\Question\Question;
use Testcenter\Domain\Question\QuestionFactory;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\QuestionType;
use Testcenter\Domain\Shared\Score;

/**
 * Converts an Eloquent Question model to a domain Question entity.
 * Delegates construction logic to QuestionFactory.
 */
class QuestionMapper
{
    public function toDomain(\App\Models\Question $model): Question
    {
        return QuestionFactory::create(
            id: (string) $model->uuid_str,
            type: QuestionType::from($model->type),
            text: new QuestionText($model->content),
            score: new Score($model->score),
            payload: $model->payload ?? [],
        );
    }
}
