<?php

namespace Testcenter\Infrastructure\Exam;

use Testcenter\Domain\Exam\Description;
use Testcenter\Domain\Exam\DurationMinutes;
use Testcenter\Domain\Exam\Exam;
use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamRepository;
use Testcenter\Domain\Exam\ExamStatus;
use Testcenter\Domain\Exam\Exception\ExamNotFoundException;
use Testcenter\Domain\Exam\Title;
use Testcenter\Infrastructure\Shared\UuidBinary;

class MysqlExamRepository implements ExamRepository
{
    public function findById(ExamID $id): Exam
    {
        $model = \App\Models\Exam::where('uuid', UuidBinary::toBin($id->value()))->first();
        if ($model === null) {
            throw new ExamNotFoundException('Exam not found: ' . $id->value());
        }

        return $this->hydrate($model);
    }

    public function create(
        Title $title,
        Description $description,
        DurationMinutes $durationMinutes,
        ExamStatus $status,
    ): ExamID {
        $id = ExamID::generate();

        \App\Models\Exam::create([
            'uuid'             => $id->value(),
            'title'            => $title->value(),
            'description'      => $description->value(),
            'duration_minutes' => $durationMinutes->value(),
            'is_active'        => $status === ExamStatus::ACTIVE,
        ]);

        return $id;
    }

    public function save(Exam $exam): void
    {
        \App\Models\Exam::where('uuid', UuidBinary::toBin($exam->id()->value()))->update([
            'title'            => $exam->getTitle()->value(),
            'description'      => $exam->getDescription()->value(),
            'duration_minutes' => $exam->getDurationMinutes()->value(),
            'is_active'        => $exam->isActive(),
        ]);
    }

    public function delete(ExamID $id): void
    {
        \App\Models\Exam::where('uuid', UuidBinary::toBin($id->value()))->delete();
    }

    private function hydrate(\App\Models\Exam $model): Exam
    {
        return new Exam(
            id: new ExamID($model->uuid_str),
            examStatus: ExamStatus::from((int) $model->is_active),
            title: new Title($model->title),
            description: new Description($model->description ?? ''),
            durationMinutes: new DurationMinutes($model->duration_minutes ?: 60),
        );
    }
}
