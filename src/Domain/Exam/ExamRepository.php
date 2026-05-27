<?php

namespace Testcenter\Domain\Exam;

use Testcenter\Domain\Exam\Exception\ExamNotFoundException;

interface ExamRepository
{
    /**
     * @throws ExamNotFoundException
     */
    public function findById(ExamID $id): Exam;

    /**
     * Persist an existing exam's state (update only).
     */
    public function save(Exam $exam): void;

    /**
     * Create a new exam and return its assigned ID.
     */
    public function create(
        Title $title,
        Description $description,
        DurationMinutes $durationMinutes,
        ExamStatus $status,
    ): ExamID;

    public function delete(ExamID $id): void;
}
