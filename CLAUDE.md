# TestCenter — DDD Project

Online exam platform built with PHP 8.1 + Laravel 10, demonstrating tactical DDD patterns in a clean layered architecture.

## Architecture

Three layers under `src/`:

```
src/
├── Domain/          # Pure PHP — zero Laravel/DB dependencies
│   ├── Shared/      # AggregateRoot, DomainEvent, Score VO
│   ├── Exam/        # Exam aggregate + ExamRepository interface
│   ├── Question/    # Question aggregate (abstract) + 6 concrete types
│   ├── Submission/  # Submission aggregate + ScoringService + Answer types
│   └── User/        # UserID value object
├── Application/     # Use case handlers (orchestration only)
│   └── Submission/UseCase/  # SubmitExamHandler + SubmitExamCommand
└── Infrastructure/  # Eloquent repositories + mapper + event publisher
    ├── Exam/        # MysqlExamRepository
    ├── Question/    # MysqlQuestionRepository + QuestionMapper
    ├── Submission/  # MysqlSubmissionRepository
    └── Shared/      # LaravelEventPublisher
```

Namespace root: `Testcenter\` → `src/`  
Laravel Eloquent models: `App\` → `app/`  
Tests: `Tests\` → `tests/`

## Domain Model

### Aggregates

- **Exam** (`Domain/Exam/Exam.php`) — lifecycle: rename, update description, publish. Fires `ExamPublished`, `ExamRenamed`, `ExamDescriptionUpdated`.
- **Question** (`Domain/Question/Question.php`) — abstract aggregate. Concrete types: `SingleChoiceQuestion`, `MultipleChoiceQuestion`, `TrueFalseQuestion`, `FillBlankQuestion`, `MatchingQuestion`, `OrderingQuestion`.
- **Submission** (`Domain/Submission/Submission.php`) — created via `Submission::submit()` (enforces active exam); scored via `applyScore()`.

All aggregates extend `AggregateRoot` which records domain events for release in the application handler.

### Key Value Objects

| Class | Invariant |
|---|---|
| `Score` | non-negative |
| `Title`, `Description` | non-empty |
| `ExamID`, `QuestionID`, `UserID` | non-empty int |
| `QuestionText` | non-empty |
| `AcceptedAnswers` | non-empty list |
| `OptionCollection` | non-empty, no duplicates |
| `FillBlankAnswer` | non-empty string |
| `MultipleChoiceAnswer` | non-empty, no duplicate choices |

### Question Grading (polymorphic)

Each `Question` subtype implements `grade(Answer): GradeResult`. No switch statements — add a new type by extending `Question`.

| Type | Scoring |
|---|---|
| SingleChoice, TrueFalse, FillBlank, Ordering | All-or-nothing |
| MultipleChoice | All-or-nothing; normalized comparison |
| Matching | Partial credit — ≥50% correct pairs = pass; score scales with accuracy |

### Domain Service

`ScoringService::score(Submission, QuestionCollection): ScoreResult` — cross-aggregate; iterates questions and delegates to `question->grade(answer)`.

## Application Layer

`SubmitExamHandler::handle(SubmitExamCommand)` orchestrates:
1. Load `Exam` via `ExamRepository`
2. Load `QuestionCollection` via `QuestionRepository`
3. Build answer objects via `Question::createAnswer()`
4. `Submission::submit()` — enforces exam active
5. `ScoringService::score()`
6. `submission->applyScore()`
7. `SubmissionRepository::save()`
8. Release + publish domain events via `DomainEventPublisher`
9. Return `SubmissionResponse(score: int)`

The handler owns no business logic — all invariants live in the domain.

## Infrastructure

- **Repositories** implement domain interfaces using Eloquent. `QuestionMapper` converts Eloquent → domain objects (JSON payloads stored per question type).
- **LaravelEventPublisher** wraps Laravel's `Dispatcher` to dispatch released domain events.
- Submission persistence is transactional: one `Submission` row + one `SubmissionAnswer` row per answer.

## Tests

Framework: PHPUnit 10.1. Run: `./vendor/bin/phpunit`

```
tests/Unit/
├── Exam/          # ExamTest — state transitions, event recording
├── Question/      # One test class per question type
└── Submission/    # SubmitExamTest — submission lifecycle, scoring
```

Tests are pure unit tests (no database, no Laravel bootstrap). Use real objects — no mocks unless testing publisher/repository boundaries.

## Common Tasks

### Add a new question type

1. Create `src/Domain/Question/Type/XxxQuestion.php` extending `Question`
2. Implement `grade(Answer): GradeResult` and `createAnswer(mixed): Answer`
3. Add enum case to `QuestionType`
4. Handle in `QuestionMapper::map()` (infrastructure)
5. Add test in `tests/Unit/Question/XxxQuestionTest.php`

### Add a new use case

1. Create `Command` DTO in `src/Application/<Aggregate>/UseCase/`
2. Create `Handler` with constructor-injected domain interfaces
3. Register handler + dependencies in a Laravel service provider

### Extend the Exam aggregate

Business rules belong in `Exam.php`. Record events via `$this->record(new SomeEvent(...))`. Release + publish happens in the application handler — not in the domain.

## Conventions

- Domain layer has **zero** `use` statements importing from `Illuminate\` or `App\`
- Validation always happens in the constructor of value objects — never in handlers or services
- Repositories return domain objects, never Eloquent models
- `QuestionMapper` is the only place that knows about Eloquent question model structure
- Tests use `new` directly — no factory helpers needed for unit tests
