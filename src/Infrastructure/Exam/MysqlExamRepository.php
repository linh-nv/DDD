<?php

namespace Testcenter\Infrastructure\Exam;

use Testcenter\Domain\Exam\Description;
use Testcenter\Domain\Exam\Exam;
use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamRepository;
use Testcenter\Domain\Exam\ExamStatus;
use Testcenter\Domain\Exam\Exception\ExamNotFoundException;
use Testcenter\Domain\Exam\Title;

class MysqlExamRepository implements ExamRepository
{
    public function findById(ExamID $id): Exam
    {
        $examEloquent = \App\Models\Exam::find($id->value());
        if ($examEloquent === null) {
            throw new ExamNotFoundException('Exam not found');
        }

        return new Exam(
            id: new ExamID($examEloquent->id),
            examStatus: ExamStatus::from($examEloquent->is_active),
            title: new Title($examEloquent->title),
            description: new Description($examEloquent->description),
        );
    }
}