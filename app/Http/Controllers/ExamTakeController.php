<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Testcenter\Infrastructure\Shared\UuidBinary;

class ExamTakeController extends Controller
{
    public function show(string $id)
    {
        $exam = Exam::with('questions')
            ->where('uuid', UuidBinary::toBin($id))
            ->firstOrFail();

        $questions = $exam->questions->map(function ($question) {
            $payload = $question->payload ?? [];

            if ($question->type === 'ordering') {
                $items = $payload['correct_order'] ?? [];
                shuffle($items);
                $question->display_items = $items;
            }

            if ($question->type === 'category') {
                $items = array_keys($payload['correct_map'] ?? []);
                shuffle($items);
                $question->display_items = $items;
            }

            return $question;
        });

        return view('exam.show', compact('exam', 'questions'));
    }
}
