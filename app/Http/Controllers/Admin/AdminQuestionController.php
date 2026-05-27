<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Testcenter\Application\Question\UseCase\CreateQuestionCommand;
use Testcenter\Application\Question\UseCase\CreateQuestionHandler;
use Testcenter\Application\Question\UseCase\DeleteQuestionCommand;
use Testcenter\Application\Question\UseCase\DeleteQuestionHandler;
use Testcenter\Application\Question\UseCase\UpdateQuestionCommand;
use Testcenter\Application\Question\UseCase\UpdateQuestionHandler;

class AdminQuestionController extends Controller
{
    public function __construct(
        private readonly CreateQuestionHandler $createHandler,
        private readonly UpdateQuestionHandler $updateHandler,
        private readonly DeleteQuestionHandler $deleteHandler,
    ) {}

    public function index()
    {
        $questions = Question::latest()->paginate(20);
        return view('admin.questions.index', compact('questions'));
    }

    public function create()
    {
        return view('admin.questions.form', ['question' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'    => ['required', 'in:true_false,single_choice,multiple_choice,fill_blank,matching,ordering,category'],
            'content' => ['required', 'string'],
            'score'   => ['required', 'integer', 'min:1'],
        ]);

        $payload = $this->buildPayload($request);

        $this->createHandler->handle(new CreateQuestionCommand(
            type: $request->input('type'),
            content: $request->input('content'),
            score: (int) $request->input('score'),
            payload: $payload,
        ));

        return redirect()->route('admin.questions.index')
            ->with('success', 'Câu hỏi đã được tạo thành công.');
    }

    public function edit(Question $question)
    {
        return view('admin.questions.form', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'type'    => ['required', 'in:true_false,single_choice,multiple_choice,fill_blank,matching,ordering,category'],
            'content' => ['required', 'string'],
            'score'   => ['required', 'integer', 'min:1'],
        ]);

        $payload = $this->buildPayload($request);

        $this->updateHandler->handle(new UpdateQuestionCommand(
            questionId: $question->id,
            type: $request->input('type'),
            content: $request->input('content'),
            score: (int) $request->input('score'),
            payload: $payload,
        ));

        return redirect()->route('admin.questions.index')
            ->with('success', 'Câu hỏi đã được cập nhật.');
    }

    public function destroy(Question $question)
    {
        $this->deleteHandler->handle(new DeleteQuestionCommand($question->id));

        return redirect()->route('admin.questions.index')
            ->with('success', 'Câu hỏi đã được xóa.');
    }

    private function buildPayload(Request $request): array
    {
        return match ($request->input('type')) {
            'true_false' => [
                'correct' => (bool) $request->input('tf_correct', '0'),
            ],
            'single_choice' => [
                'options' => $this->buildOptions(
                    $request->input('sc_key', []),
                    $request->input('sc_label', [])
                ),
                'correct' => trim($request->input('sc_correct', '')),
            ],
            'multiple_choice' => [
                'options' => $this->buildOptions(
                    $request->input('mc_key', []),
                    $request->input('mc_label', [])
                ),
                'correct' => array_values(array_filter(
                    array_map('trim', explode(',', $request->input('mc_correct', '')))
                )),
            ],
            'fill_blank' => [
                'answers' => array_values(array_filter(
                    array_map('trim', $request->input('fb_answers', []))
                )),
            ],
            'matching' => [
                'pairs' => $this->buildPairs(
                    $request->input('match_left', []),
                    $request->input('match_right', [])
                ),
            ],
            'ordering' => [
                'correct_order' => array_values(array_filter(
                    array_map('trim', $request->input('ord_items', []))
                )),
            ],
            'category' => [
                'categories' => array_values(array_filter(
                    array_map('trim', $request->input('cat_categories', []))
                )),
                'correct_map' => $this->buildCategoryMap(
                    $request->input('cat_items', []),
                    $request->input('cat_item_categories', [])
                ),
            ],
            default => [],
        };
    }

    private function buildOptions(array $keys, array $labels): array
    {
        $opts = [];
        foreach ($keys as $i => $key) {
            $key = trim($key);
            if ($key !== '') {
                $opts[$key] = $labels[$i] ?? '';
            }
        }
        return $opts;
    }

    private function buildPairs(array $lefts, array $rights): array
    {
        $pairs = [];
        foreach ($lefts as $i => $left) {
            $left  = trim($left);
            $right = trim($rights[$i] ?? '');
            if ($left !== '' && $right !== '') {
                $pairs[$left] = $right;
            }
        }
        return $pairs;
    }

    private function buildCategoryMap(array $items, array $categories): array
    {
        $map = [];
        foreach ($items as $i => $item) {
            $item = trim($item);
            $cat  = trim($categories[$i] ?? '');
            if ($item !== '' && $cat !== '') {
                $map[$item] = $cat;
            }
        }
        return $map;
    }
}
