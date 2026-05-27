<?php

namespace Testcenter\Application\Exam\UseCase;

class ReorderExamQuestionsCommand
{
    /**
     * @param array<string, int> $questionSortOrders  [questionId => sortOrder]
     */
    public function __construct(
        public readonly string $examId,
        public readonly array $questionSortOrders,
    ) {}
}
