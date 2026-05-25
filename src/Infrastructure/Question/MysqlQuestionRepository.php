<?php

namespace Testcenter\Infrastructure\Question;

use App\Models\Question;
use Testcenter\Domain\Question\Exception\QuestionNotFoundException;
use Testcenter\Domain\Question\Question as QuestionEntity;
use Testcenter\Domain\Question\QuestionCollection;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionRepository;

class MysqlQuestionRepository implements QuestionRepository
{
    public function __construct(
        private readonly QuestionMapper $questionMapper
    ) {
    }

    public function findQuestionsForExam(array $ids): QuestionCollection
    {
        $rawIds = array_map(fn(QuestionID $id) => $id->value(), $ids);
        $questionEloquents = Question::query()
            ->whereIn('id', $rawIds)
            ->get();

        $questionEntities = [];
        foreach ($questionEloquents as $questionEloquent) {
            $questionEntities[] = $this->questionMapper->toDomain($questionEloquent);
        }

        return new QuestionCollection($questionEntities);
    }

    public function findById(QuestionID $id): QuestionEntity
    {
        $questionEloquent = Question::find($id->value());
        if ($questionEloquent === null) {
            throw new QuestionNotFoundException('Question not found');
        }

        return $this->questionMapper->toDomain($questionEloquent);
    }
}