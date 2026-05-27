<?php

namespace Tests\Unit\Submission;

use PHPUnit\Framework\TestCase;
use Testcenter\Application\Submission\UseCase\SubmitExamCommand;
use Testcenter\Application\Submission\UseCase\SubmitExamHandler;
use Testcenter\Domain\Exam\Description;
use Testcenter\Domain\Exam\Exam;
use Testcenter\Domain\Exam\DurationMinutes;
use Testcenter\Domain\Exam\ExamID;
use Testcenter\Domain\Exam\ExamRepository;
use Testcenter\Domain\Exam\ExamStatus;
use Testcenter\Domain\Exam\Exception\ExamNotActiveException;
use Testcenter\Domain\Exam\Exception\ExamNotFoundException;
use Testcenter\Domain\Exam\Title;
use Testcenter\Domain\Question\AcceptedAnswers;
use Testcenter\Domain\Question\Category\Categories;
use Testcenter\Domain\Question\Category\CategoryMap;
use Testcenter\Domain\Question\Exception\QuestionNotFoundException;
use Testcenter\Domain\Question\OptionCollection;
use Testcenter\Domain\Question\Pair\MatchingPair;
use Testcenter\Domain\Question\Pair\MatchingPairs;
use Testcenter\Domain\Question\QuestionCollection;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionRepository;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\Type\CategoryQuestion;
use Testcenter\Domain\Question\Type\FillBlankQuestion;
use Testcenter\Domain\Question\Type\MatchingQuestion;
use Testcenter\Domain\Question\Type\MultipleChoiceQuestion;
use Testcenter\Domain\Question\Type\OrderingQuestion;
use Testcenter\Domain\Question\Type\SingleChoiceQuestion;
use Testcenter\Domain\Question\Type\TrueFalseQuestion;
use Testcenter\Domain\Shared\DomainEventPublisher;
use Testcenter\Domain\Shared\Score;
use Testcenter\Domain\Submission\Event\ExamSubmitted;
use Testcenter\Domain\Submission\Service\ScoringService;
use Testcenter\Domain\Submission\SubmissionRepository;

class SubmitExamHandlerTest extends TestCase
{
    private const EXAM_ID = 'b0000000-0000-0000-0000-000000000001';
    private const Q1      = 'a0000000-0000-0000-0000-000000000001';
    private const Q2      = 'a0000000-0000-0000-0000-000000000002';
    private const Q3      = 'a0000000-0000-0000-0000-000000000003';
    private const Q4      = 'a0000000-0000-0000-0000-000000000004';
    private const Q5      = 'a0000000-0000-0000-0000-000000000005';
    private const Q6      = 'a0000000-0000-0000-0000-000000000006';
    private const Q7      = 'a0000000-0000-0000-0000-000000000007';

    private ExamRepository $examRepo;
    private QuestionRepository $questionRepo;
    private SubmissionRepository $submissionRepo;
    private DomainEventPublisher $publisher;
    private SubmitExamHandler $handler;

    protected function setUp(): void
    {
        $this->examRepo       = $this->createMock(ExamRepository::class);
        $this->questionRepo   = $this->createMock(QuestionRepository::class);
        $this->submissionRepo = $this->createMock(SubmissionRepository::class);
        $this->publisher      = $this->createMock(DomainEventPublisher::class);

        $this->handler = new SubmitExamHandler(
            $this->examRepo,
            $this->questionRepo,
            $this->submissionRepo,
            new ScoringService(),
            $this->publisher,
        );
    }

    private function activeExam(): Exam
    {
        return new Exam(
            id: new ExamID(self::EXAM_ID),
            examStatus: ExamStatus::ACTIVE,
            title: new Title('PHP Test'),
            description: new Description('Basic PHP knowledge test'),
            durationMinutes: new DurationMinutes(60),
        );
    }

    private function inactiveExam(): Exam
    {
        return new Exam(
            id: new ExamID(self::EXAM_ID),
            examStatus: ExamStatus::INACTIVE,
            title: new Title('PHP Test'),
            description: new Description('Basic PHP knowledge test'),
            durationMinutes: new DurationMinutes(60),
        );
    }

    private function singleChoiceQuestion(string $id = self::Q1, int $score = 5): SingleChoiceQuestion
    {
        return new SingleChoiceQuestion(
            id: new QuestionID($id),
            text: new QuestionText('What does PHP stand for?'),
            score: new Score($score),
            options: new OptionCollection(['A' => 'PHP: Hypertext Preprocessor', 'B' => 'Personal Home Page']),
            correct: 'A',
        );
    }

    public function test_it_returns_full_score_when_answer_is_correct(): void
    {
        $this->examRepo->method('findById')->willReturn($this->activeExam());
        $this->questionRepo->method('findQuestionsForExam')
            ->willReturn(new QuestionCollection([$this->singleChoiceQuestion(score: 5)]));

        $response = $this->handler->handle(
            new SubmitExamCommand(examId: self::EXAM_ID, userId: 1, answers: [self::Q1 => 'A'])
        );

        $this->assertEquals(5, $response->score);
    }

    public function test_it_returns_zero_when_answer_is_wrong(): void
    {
        $this->examRepo->method('findById')->willReturn($this->activeExam());
        $this->questionRepo->method('findQuestionsForExam')
            ->willReturn(new QuestionCollection([$this->singleChoiceQuestion(score: 5)]));

        $response = $this->handler->handle(
            new SubmitExamCommand(examId: self::EXAM_ID, userId: 1, answers: [self::Q1 => 'B'])
        );

        $this->assertEquals(0, $response->score);
    }

    public function test_it_persists_the_submission(): void
    {
        $this->examRepo->method('findById')->willReturn($this->activeExam());
        $this->questionRepo->method('findQuestionsForExam')
            ->willReturn(new QuestionCollection([$this->singleChoiceQuestion()]));

        $this->submissionRepo->expects($this->once())->method('save');

        $this->handler->handle(
            new SubmitExamCommand(examId: self::EXAM_ID, userId: 1, answers: [self::Q1 => 'A'])
        );
    }

    public function test_it_publishes_exam_submitted_event(): void
    {
        $this->examRepo->method('findById')->willReturn($this->activeExam());
        $this->questionRepo->method('findQuestionsForExam')
            ->willReturn(new QuestionCollection([$this->singleChoiceQuestion()]));

        $this->publisher
            ->expects($this->once())
            ->method('publish')
            ->with($this->isInstanceOf(ExamSubmitted::class));

        $this->handler->handle(
            new SubmitExamCommand(examId: self::EXAM_ID, userId: 1, answers: [self::Q1 => 'A'])
        );
    }

    public function test_it_scores_all_question_types_correctly(): void
    {
        $categories = new Categories(['Frontend', 'Backend', 'Database']);

        $questions = new QuestionCollection([
            new TrueFalseQuestion(
                id: new QuestionID(self::Q1),
                text: new QuestionText('PHP is weakly typed'),
                score: new Score(1),
                correct: true,
            ),
            new SingleChoiceQuestion(
                id: new QuestionID(self::Q2),
                text: new QuestionText('Laravel is written in?'),
                score: new Score(2),
                options: new OptionCollection(['A' => 'Java', 'B' => 'PHP', 'C' => 'Go']),
                correct: 'B',
            ),
            new MultipleChoiceQuestion(
                id: new QuestionID(self::Q3),
                text: new QuestionText('Which are PHP frameworks?'),
                score: new Score(2),
                options: new OptionCollection(['A' => 'Laravel', 'B' => 'Symfony', 'C' => 'Django']),
                correct: ['A', 'B'],
            ),
            new FillBlankQuestion(
                id: new QuestionID(self::Q4),
                text: new QuestionText('The __ pattern separates domain from infrastructure'),
                score: new Score(2),
                acceptedAnswers: new AcceptedAnswers(['repository', 'Repository']),
            ),
            new MatchingQuestion(
                id: new QuestionID(self::Q5),
                text: new QuestionText('Match tech to layer'),
                score: new Score(3),
                pairs: new MatchingPairs([
                    new MatchingPair('Vue', 'Frontend'),
                    new MatchingPair('Laravel', 'Backend'),
                    new MatchingPair('MySQL', 'Database'),
                ]),
            ),
            new OrderingQuestion(
                id: new QuestionID(self::Q6),
                text: new QuestionText('Order HTTP lifecycle steps'),
                score: new Score(3),
                correctOrder: ['Request', 'Middleware', 'Controller', 'Response'],
            ),
            new CategoryQuestion(
                id: new QuestionID(self::Q7),
                text: new QuestionText('Classify technologies'),
                score: new Score(4),
                categories: $categories,
                correctMap: new CategoryMap([
                    'Vue'     => 'Frontend',
                    'Laravel' => 'Backend',
                    'MySQL'   => 'Database',
                ], $categories),
            ),
        ]);

        $this->examRepo->method('findById')->willReturn($this->activeExam());
        $this->questionRepo->method('findQuestionsForExam')->willReturn($questions);

        $response = $this->handler->handle(new SubmitExamCommand(
            examId: self::EXAM_ID,
            userId: 1,
            answers: [
                self::Q1 => true,
                self::Q2 => 'B',
                self::Q3 => ['A', 'B'],
                self::Q4 => 'repository',
                self::Q5 => ['Vue' => 'Frontend', 'Laravel' => 'Backend', 'MySQL' => 'Database'],
                self::Q6 => ['Request', 'Middleware', 'Controller', 'Response'],
                self::Q7 => ['Vue' => 'Frontend', 'Laravel' => 'Backend', 'MySQL' => 'Database'],
            ],
        ));

        $this->assertEquals(17, $response->score); // 1+2+2+2+3+3+4
    }

    public function test_matching_gives_partial_score_for_half_correct_pairs(): void
    {
        $question = new MatchingQuestion(
            id: new QuestionID(self::Q1),
            text: new QuestionText('Match tech to layer'),
            score: new Score(4),
            pairs: new MatchingPairs([
                new MatchingPair('Vue', 'Frontend'),
                new MatchingPair('Laravel', 'Backend'),
                new MatchingPair('MySQL', 'Database'),
                new MatchingPair('Redis', 'Cache'),
            ]),
        );

        $this->examRepo->method('findById')->willReturn($this->activeExam());
        $this->questionRepo->method('findQuestionsForExam')
            ->willReturn(new QuestionCollection([$question]));

        $response = $this->handler->handle(new SubmitExamCommand(
            examId: self::EXAM_ID,
            userId: 1,
            answers: [
                self::Q1 => [
                    'Vue'     => 'Frontend',
                    'Laravel' => 'Backend',
                    'MySQL'   => 'Frontend',
                    'Redis'   => 'Backend',
                ],
            ],
        ));

        $this->assertEquals(2, $response->score); // 2/4 * 4 = 2
    }

    public function test_category_gives_partial_score_for_half_correct_assignments(): void
    {
        $categories = new Categories(['Frontend', 'Backend', 'Database']);
        $question = new CategoryQuestion(
            id: new QuestionID(self::Q1),
            text: new QuestionText('Classify technologies'),
            score: new Score(4),
            categories: $categories,
            correctMap: new CategoryMap([
                'Vue'     => 'Frontend',
                'React'   => 'Frontend',
                'Laravel' => 'Backend',
                'MySQL'   => 'Database',
            ], $categories),
        );

        $this->examRepo->method('findById')->willReturn($this->activeExam());
        $this->questionRepo->method('findQuestionsForExam')
            ->willReturn(new QuestionCollection([$question]));

        $response = $this->handler->handle(new SubmitExamCommand(
            examId: self::EXAM_ID,
            userId: 1,
            answers: [
                self::Q1 => [
                    'Vue'     => 'Frontend',
                    'React'   => 'Frontend',
                    'Laravel' => 'Database',
                    'MySQL'   => 'Backend',
                ],
            ],
        ));

        $this->assertEquals(2, $response->score); // 2/4 * 4 = 2
    }

    public function test_it_throws_when_exam_is_not_found(): void
    {
        $this->examRepo
            ->method('findById')
            ->willThrowException(new ExamNotFoundException('Exam not found'));

        $this->expectException(ExamNotFoundException::class);

        $this->handler->handle(
            new SubmitExamCommand(examId: 'c0000000-0000-0000-0000-000000000099', userId: 1, answers: [self::Q1 => 'A'])
        );
    }

    public function test_it_throws_when_exam_is_not_active(): void
    {
        $this->examRepo->method('findById')->willReturn($this->inactiveExam());
        $this->questionRepo->method('findQuestionsForExam')
            ->willReturn(new QuestionCollection([$this->singleChoiceQuestion()]));

        $this->expectException(ExamNotActiveException::class);

        $this->handler->handle(
            new SubmitExamCommand(examId: self::EXAM_ID, userId: 1, answers: [self::Q1 => 'A'])
        );
    }

    public function test_it_throws_when_answer_refers_to_unknown_question(): void
    {
        $this->examRepo->method('findById')->willReturn($this->activeExam());
        $this->questionRepo->method('findQuestionsForExam')
            ->willReturn(new QuestionCollection([]));

        $this->expectException(QuestionNotFoundException::class);

        $this->handler->handle(
            new SubmitExamCommand(examId: self::EXAM_ID, userId: 1, answers: ['c0000000-0000-0000-0000-000000000099' => 'A'])
        );
    }
}
