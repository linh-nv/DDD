# Run Domain Tests

Run the unit test suite for the TestCenter DDD project and report results.

## Steps

1. Run all unit tests:
```bash
./vendor/bin/phpunit --no-coverage
```

2. If a specific aggregate or class is given in `$ARGUMENTS`, scope the run:
```bash
./vendor/bin/phpunit tests/Unit/$ARGUMENTS --no-coverage
```

3. Report:
   - Total tests / assertions / failures
   - Any failing test names with the failure message
   - Any domain invariant violations caught (look for `InvalidArgumentException`, `ExamNotActiveException`, `QuestionNotFoundException`)

## Test structure

```
tests/Unit/
├── Exam/          ExamTest — lifecycle, events (ExamPublished, ExamRenamed, ExamDescriptionUpdated)
├── Question/      One test class per question type (7 types + CategoryQuestion)
└── Submission/    SubmitExamTest — aggregate lifecycle
                   SubmitExamHandlerTest — full handler integration with mocked repositories
```

## Testing conventions in this project

- Use real domain objects — `new QuestionID(1)`, `new Score(5)`, etc.
- Mock only infrastructure boundaries: `ExamRepository`, `QuestionRepository`, `SubmissionRepository`, `DomainEventPublisher`
- `ScoringService` is used as a real instance (pure domain, no infrastructure)
- No database, no Laravel bootstrap for unit tests
- `MatchingPairs` requires unique left **and** unique right values — use distinct strings for both sides
