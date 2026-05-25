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
}