# Add a new Use Case

Add a complete application use case to the TestCenter DDD project.

## What you will build

Given a use case name `$ARGUMENTS` (e.g. `PublishExam`, `RenameQuestion`), create:

1. **Command DTO** at `src/Application/<Aggregate>/UseCase/<Name>Command.php`
   - Plain PHP class, public constructor properties
   - Primitive types only — no domain objects
2. **Handler** at `src/Application/<Aggregate>/UseCase/<Name>Handler.php`
   - Constructor-injected domain interfaces only
   - Orchestration only: load → validate via domain → persist → publish events → return response
   - Zero business logic — all rules live in the domain
3. **Response DTO** (if needed) at `src/Application/<Aggregate>/<Name>Response.php`
4. **Service Provider binding** in `app/Providers/AppServiceProvider.php`
   - Bind any new repository interfaces to their infrastructure implementations
5. **API route** in `routes/api.php` pointing to `ExamController` or a new controller
6. **Controller action** in `app/Http/Controllers/` — validate request, build Command, call Handler, return JSON

## Handler structure to follow

```php
public function handle(XxxCommand $command): XxxResponse
{
    // 1. Load aggregates via repositories
    // 2. Call domain method (enforces invariants, records events)
    // 3. Persist via repository
    // 4. Release + publish domain events
    // 5. Return response DTO
    $this->publisher->publish(...$aggregate->releaseEvents());
    return new XxxResponse(...);
}
```

## Conventions

- Handler owns **no** business logic — if you find yourself writing an `if` that enforces a rule, move it to the domain
- Repositories return domain objects, never Eloquent models
- Events are released and published in the handler, not in the domain
- Validation (request-level) belongs in the controller, not the handler

## Aggregates available

| Aggregate | Repository interface | Key methods |
|---|---|---|
| `Exam` | `ExamRepository` | `findById(ExamID)` |
| `Question` | `QuestionRepository` | `findQuestionsForExam(QuestionID[])`, `findById(QuestionID)` |
| `Submission` | `SubmissionRepository` | `save(Submission)` |

## Infrastructure bindings (AppServiceProvider)

```php
$this->app->singleton(ExamRepository::class, MysqlExamRepository::class);
$this->app->singleton(QuestionRepository::class, MysqlQuestionRepository::class);
$this->app->singleton(SubmissionRepository::class, MysqlSubmissionRepository::class);
$this->app->singleton(DomainEventPublisher::class, LaravelEventPublisher::class);
```
