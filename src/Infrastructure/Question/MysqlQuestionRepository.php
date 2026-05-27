<?php

namespace Testcenter\Infrastructure\Question;

use App\Models\Question as QuestionModel;
use Testcenter\Domain\Question\Exception\QuestionNotFoundException;
use Testcenter\Domain\Question\Question as QuestionEntity;
use Testcenter\Domain\Question\QuestionCollection;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionRepository;
use Testcenter\Infrastructure\Shared\UuidBinary;

class MysqlQuestionRepository implements QuestionRepository
{
    public function __construct(
        private readonly QuestionMapper $questionMapper,
    ) {}

    public function findQuestionsForExam(array $ids): QuestionCollection
    {
        $binIds = array_map(fn(QuestionID $id) => UuidBinary::toBin($id->value()), $ids);

        $entities = QuestionModel::query()
            ->whereIn('uuid', $binIds)
            ->get()
            ->map(fn($m) => $this->questionMapper->toDomain($m))
            ->all();

        return new QuestionCollection($entities);
    }

    public function findById(QuestionID $id): QuestionEntity
    {
        $model = QuestionModel::where('uuid', UuidBinary::toBin($id->value()))->first();
        if ($model === null) {
            throw new QuestionNotFoundException('Question not found: ' . $id->value());
        }

        return $this->questionMapper->toDomain($model);
    }

    public function save(QuestionEntity $question): void
    {
        $payload = $this->extractPayload($question);
        $binId   = UuidBinary::toBin($question->id()->value());

        $exists = QuestionModel::where('uuid', $binId)->exists();

        if (!$exists) {
            QuestionModel::create([
                'uuid'           => $question->id()->value(),
                'type'           => $question->type()->value,
                'content'        => $question->text()->value(),
                'score'          => (int) $question->score()->value(),
                'payload'        => $payload,
                'correct_answer' => $payload['_summary'] ?? null,
            ]);
        } else {
            QuestionModel::where('uuid', $binId)->update([
                'type'           => $question->type()->value,
                'content'        => $question->text()->value(),
                'score'          => (int) $question->score()->value(),
                'payload'        => json_encode($payload),
                'correct_answer' => $payload['_summary'] ?? null,
            ]);
        }
    }

    public function delete(QuestionID $id): void
    {
        QuestionModel::where('uuid', UuidBinary::toBin($id->value()))->delete();
    }

    private function extractPayload(QuestionEntity $question): array
    {
        return $question->toPayload();
    }
}
