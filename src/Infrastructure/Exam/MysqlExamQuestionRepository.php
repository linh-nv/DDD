<?php

namespace Testcenter\Infrastructure\Exam;

use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamQuestionRepository;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Infrastructure\Shared\UuidBinary;

class MysqlExamQuestionRepository implements ExamQuestionRepository
{
    public function add(ExamID $examId, QuestionID $questionId, int $sortOrder): void
    {
        $exam = \App\Models\Exam::where('uuid', UuidBinary::toBin($examId->value()))->first();
        $exam?->questions()->attach(
            UuidBinary::toBin($questionId->value()),
            ['sort_order' => $sortOrder]
        );
    }

    public function remove(ExamID $examId, QuestionID $questionId): void
    {
        $exam = \App\Models\Exam::where('uuid', UuidBinary::toBin($examId->value()))->first();
        $exam?->questions()->detach(UuidBinary::toBin($questionId->value()));
    }

    public function reorder(ExamID $examId, array $questionSortOrders): void
    {
        $exam = \App\Models\Exam::where('uuid', UuidBinary::toBin($examId->value()))->first();
        if ($exam === null) {
            return;
        }

        foreach ($questionSortOrders as $questionId => $sortOrder) {
            $exam->questions()->updateExistingPivot(
                UuidBinary::toBin($questionId),
                ['sort_order' => (int) $sortOrder]
            );
        }
    }

    public function maxSortOrder(ExamID $examId): int
    {
        return (int) \Illuminate\Support\Facades\DB::table('exam_questions')
            ->where('exam_id', UuidBinary::toBin($examId->value()))
            ->max('sort_order');
    }

    public function exists(ExamID $examId, QuestionID $questionId): bool
    {
        return \Illuminate\Support\Facades\DB::table('exam_questions')
            ->where('exam_id', UuidBinary::toBin($examId->value()))
            ->where('question_id', UuidBinary::toBin($questionId->value()))
            ->exists();
    }

    public function removeAll(ExamID $examId): void
    {
        $exam = \App\Models\Exam::where('uuid', UuidBinary::toBin($examId->value()))->first();
        $exam?->questions()->detach();
    }
}
