<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamTakeController extends Controller
{
    public function show(int $id)
    {
        $exam = Exam::with('questions')->findOrFail($id);

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
