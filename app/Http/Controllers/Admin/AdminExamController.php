<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Testcenter\Application\Exam\UseCase\AddQuestionToExamCommand;
use Testcenter\Application\Exam\UseCase\AddQuestionToExamHandler;
use Testcenter\Application\Exam\UseCase\CreateExamCommand;
use Testcenter\Application\Exam\UseCase\CreateExamHandler;
use Testcenter\Application\Exam\UseCase\DeleteExamCommand;
use Testcenter\Application\Exam\UseCase\DeleteExamHandler;
use Testcenter\Application\Exam\UseCase\PublishExamCommand;
use Testcenter\Application\Exam\UseCase\PublishExamHandler;
use Testcenter\Application\Exam\UseCase\RemoveQuestionFromExamCommand;
use Testcenter\Application\Exam\UseCase\RemoveQuestionFromExamHandler;
use Testcenter\Application\Exam\UseCase\ReorderExamQuestionsCommand;
use Testcenter\Application\Exam\UseCase\ReorderExamQuestionsHandler;
use Testcenter\Application\Exam\UseCase\UnpublishExamCommand;
use Testcenter\Application\Exam\UseCase\UnpublishExamHandler;
use Testcenter\Application\Exam\UseCase\UpdateExamCommand;
use Testcenter\Application\Exam\UseCase\UpdateExamHandler;
use Testcenter\Infrastructure\Shared\UuidBinary;

class AdminExamController extends Controller
{
    public function __construct(
        private readonly CreateExamHandler $createExamHandler,
        private readonly UpdateExamHandler $updateExamHandler,
        private readonly PublishExamHandler $publishExamHandler,
        private readonly UnpublishExamHandler $unpublishExamHandler,
        private readonly DeleteExamHandler $deleteExamHandler,
        private readonly AddQuestionToExamHandler $addQuestionHandler,
        private readonly RemoveQuestionFromExamHandler $removeQuestionHandler,
        private readonly ReorderExamQuestionsHandler $reorderHandler,
    ) {}

    public function index()
    {
        $exams = Exam::withCount(['questions', 'submissions'])->latest()->paginate(20);
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('admin.exams.form', ['exam' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $this->createExamHandler->handle(new CreateExamCommand(
            title: $request->input('title'),
            description: $request->input('description') ?? '',
            durationMinutes: (int) $request->input('duration_minutes'),
            isActive: $request->boolean('is_active'),
        ));

        return redirect()->route('admin.exams.index')
            ->with('success', 'Đề thi đã được tạo thành công.');
    }

    public function edit(Exam $exam)
    {
        return view('admin.exams.form', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $this->updateExamHandler->handle(new UpdateExamCommand(
            examId: $exam->uuid_str,
            title: $request->input('title'),
            description: $request->input('description') ?? '',
            durationMinutes: (int) $request->input('duration_minutes'),
        ));

        if ($request->boolean('is_active')) {
            $this->publishExamHandler->handle(new PublishExamCommand($exam->uuid_str));
        } else {
            $this->unpublishExamHandler->handle(new UnpublishExamCommand($exam->uuid_str));
        }

        return redirect()->route('admin.exams.index')
            ->with('success', 'Đề thi đã được cập nhật.');
    }

    public function destroy(Exam $exam)
    {
        $this->deleteExamHandler->handle(new DeleteExamCommand($exam->uuid_str));

        return redirect()->route('admin.exams.index')
            ->with('success', 'Đề thi đã được xóa.');
    }

    public function toggleActive(Exam $exam)
    {
        if ($exam->is_active) {
            $this->unpublishExamHandler->handle(new UnpublishExamCommand($exam->uuid_str));
            $msg = 'Đề thi đã bị ẩn.';
        } else {
            $this->publishExamHandler->handle(new PublishExamCommand($exam->uuid_str));
            $msg = 'Đề thi đã được công bố.';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function questions(Exam $exam)
    {
        $examQuestions      = $exam->questions()->orderByPivot('sort_order')->get();
        $examQuestionBinIds = $examQuestions->map(fn($q) => $q->getKey())->toArray();
        $availableQuestions = Question::whereNotIn('uuid', $examQuestionBinIds)->get();

        return view('admin.exams.questions', compact('exam', 'examQuestions', 'availableQuestions'));
    }

    public function addQuestion(Request $request, Exam $exam)
    {
        $request->validate([
            'question_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!\App\Models\Question::where('uuid', UuidBinary::toBin($value))->exists()) {
                        $fail('The selected question id is invalid.');
                    }
                },
            ],
        ]);

        $this->addQuestionHandler->handle(new AddQuestionToExamCommand(
            examId: $exam->uuid_str,
            questionId: (string) $request->input('question_id'),
        ));

        return redirect()->route('admin.exams.questions', $exam)
            ->with('success', 'Câu hỏi đã được thêm vào đề thi.');
    }

    public function removeQuestion(Exam $exam, Question $question)
    {
        $this->removeQuestionHandler->handle(new RemoveQuestionFromExamCommand(
            examId: $exam->uuid_str,
            questionId: $question->uuid_str,
        ));

        return redirect()->route('admin.exams.questions', $exam)
            ->with('success', 'Câu hỏi đã được xóa khỏi đề thi.');
    }

    public function reorderQuestions(Request $request, Exam $exam)
    {
        $request->validate(['order' => ['required', 'array']]);

        $this->reorderHandler->handle(new ReorderExamQuestionsCommand(
            examId: $exam->uuid_str,
            questionSortOrders: array_map('intval', $request->input('order')),
        ));

        return redirect()->route('admin.exams.questions', $exam)
            ->with('success', 'Thứ tự câu hỏi đã được lưu.');
    }
}
