<?php

namespace Testcenter\Domain\Exam;

use Testcenter\Domain\Question\QuestionID;

interface ExamQuestionRepository
{
    public function add(ExamID $examId, QuestionID $questionId, int $sortOrder): void;

    public function remove(ExamID $examId, QuestionID $questionId): void;

    /**
     * @param array<int, int> $questionSortOrders  [questionId => sortOrder]
     */
    public function reorder(ExamID $examId, array $questionSortOrders): void;

    public function maxSortOrder(ExamID $examId): int;

    public function exists(ExamID $examId, QuestionID $questionId): bool;

    public function removeAll(ExamID $examId): void;
}
