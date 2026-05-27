<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Submission;
use App\Models\SubmissionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Testcenter\Application\Submission\UseCase\SubmitExamCommand;
use Testcenter\Application\Submission\UseCase\SubmitExamHandler;

class ExamController
{
    public function __construct(
        private readonly SubmitExamHandler $submitHandler
    ) {
    }

    /**
     * MVP VERSION
     * Traditional MVC style
     * Intentionally messy for workshop
     */
    public function submit(Request $request)
    {
        $request->validate([
            'exam_id' => ['required', 'integer'],
            'answers' => ['required', 'array'],
        ]);

        $examId = $request->exam_id;
        $answers = $request->answers;

        $userId = Auth::id();

        $questions = Question::whereIn(
            'id',
            array_keys($answers)
        )->get();

        $totalScore = 0;

        DB::beginTransaction();

        try {
            foreach ($questions as $question) {
                $answer = $answers[$question->id] ?? null;

                // =====================================
                // TRUE FALSE
                // =====================================
                if ($question->type === 'true_false') {
                    if (
                        (bool)$answer ===
                        (bool)$question->correct_answer
                    ) {
                        $totalScore += $question->score;
                    }
                }

                // =====================================
                // SINGLE CHOICE
                // =====================================
                if ($question->type === 'single_choice') {
                    if ($answer === $question->correct_answer) {
                        $totalScore += $question->score;
                    }
                }

                // =====================================
                // FILL BLANK
                // =====================================
                if ($question->type === 'fill_blank') {
                    if (
                        strtolower(trim($answer)) ===
                        strtolower(trim($question->correct_answer))
                    ) {
                        $totalScore += $question->score;
                    }
                }

                // =====================================
                // MULTIPLE CHOICE
                // =====================================
                if ($question->type === 'multiple_choice') {
                    $normalize = fn(array $a) => array_values(array_unique(array_map('strval', $a)));
                    $correct = $normalize($question->payload['correct'] ?? []);
                    $submitted = $normalize((array)$answer);
                    sort($correct);
                    sort($submitted);

                    if ($correct === $submitted) {
                        $totalScore += $question->score;
                    }
                }

                // =====================================
                // ORDERING
                // =====================================
                if ($question->type === 'ordering') {
                    $correctOrder = array_values($question->payload['correct_order'] ?? []);
                    $submittedOrder = array_values((array)$answer);

                    if ($correctOrder === $submittedOrder) {
                        $totalScore += $question->score;
                    }
                }

                // =====================================
                // MATCHING
                // =====================================
                if ($question->type === 'matching') {
                    $correctPairs = $question->payload['pairs'] ?? [];

                    $correctCount = 0;

                    foreach ($answer as $left => $right) {
                        if (
                            isset($correctPairs[$left]) &&
                            $correctPairs[$left] === $right
                        ) {
                            $correctCount++;
                        }
                    }

                    $totalPairs = count($correctPairs);

                    if ($totalPairs > 0) {
                        // partial scoring
                        $partialScore = (
                                $correctCount / $totalPairs
                            ) * $question->score;

                        $totalScore += floor($partialScore);
                    }
                }

                // =====================================
                // CATEGORY
                // =====================================
                if ($question->type === 'category') {
                    $correctMap = $question->payload['correct_map'] ?? [];

                    $correctCount = 0;

                    foreach ((array)$answer as $item => $category) {
                        if (
                            isset($correctMap[$item]) &&
                            $correctMap[$item] === $category
                        ) {
                            $correctCount++;
                        }
                    }

                    $total = count($correctMap);

                    if ($total > 0) {
                        $partialScore = ($correctCount / $total) * $question->score;
                        $totalScore += floor($partialScore);
                    }
                }
            }

            // =====================================
            // SAVE SUBMISSION
            // =====================================

            $submission = Submission::create([
                'exam_id' => $examId,
                'user_id' => $userId,
                'score' => $totalScore,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // =====================================
            // SAVE ANSWERS
            // =====================================

            foreach ($answers as $questionId => $answer) {
                SubmissionAnswer::create([
                    'submission_id' => $submission->id,
                    'question_id' => $questionId,
                    'answer' => json_encode($answer),
                    'score' => 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Submit exam successfully',
                'data' => [
                    'submission_id' => $submission->id,
                    'score' => $totalScore,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function submit_ddd(Request $request)
    {
        $request->validate([
            'exam_id' => ['required', 'integer'],
            'answers' => ['required', 'array'],
        ]);

        $submissionResponse = $this->submitHandler->handle(
            new SubmitExamCommand(
                examId: $request->exam_id,
                userId: Auth::id(),
                answers: $request->answers,
            )
        );

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Submit exam successfully',
                'exam_id' => $request->exam_id,
                'score' => $submissionResponse->score,
            ],
        ]);
    }
}