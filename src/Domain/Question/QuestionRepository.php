<?php

namespace Testcenter\Domain\Question;

use Testcenter\Domain\Question\Exception\QuestionNotFoundException;

interface QuestionRepository
{
    /**
     * @param QuestionID[] $ids
     */
    public function findQuestionsForExam(array $ids): QuestionCollection;

    /**
     * @throws QuestionNotFoundException
     */
    public function findById(QuestionID $id): Question;

    /**
     * Persist a question (insert or update based on whether ID already exists).
     */
    public function save(Question $question): void;

    public function delete(QuestionID $id): void;
}
