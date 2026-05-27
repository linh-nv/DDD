# Add a new Question Type

Add a complete new question type to the TestCenter DDD project.

## What you will build

Given a question type name `$ARGUMENTS`, create all the pieces needed:

1. **Domain value objects** (if needed) under `src/Domain/Question/<TypeName>/`
2. **Answer class** at `src/Domain/Submission/Answer/<TypeName>Answer.php` implementing `Answer`
3. **Question class** at `src/Domain/Question/Type/<TypeName>Question.php` extending `Question`
   - Constructor accepts domain-specific VOs
   - `grade(Answer): GradeResult` — encapsulates scoring logic (no switch statements)
   - `createAnswer(mixed): Answer` — converts raw user input to Answer VO
4. **Enum case** added to `src/Domain/Question/QuestionType.php`
5. **Mapper case** added to `src/Infrastructure/Question/QuestionMapper.php`
   - Read from `$questionEloquent->payload[...]`
   - Use a private helper method if construction is multi-step (see `buildCategoryQuestion`)
6. **MVP controller block** added to `app/Http/Controllers/ExamController.php` in `submit()`
   - Match on `$question->type === '<snake_case>'`
   - Read correct answer from `$question->payload[...]`
7. **Seeder rows** appended to `database/seeders/QuestionSeeder.php` and linked in `ExamSeeder.php`
   - `payload` JSON must match what `QuestionMapper` expects
8. **Frontend support** added to `resources/views/exam/show.blade.php`
   - New `@elseif($question->type === '<snake_case>')` block with appropriate HTML inputs
   - JS `case '<snake_case>':` block inside `collectAnswers()` returning the correct answer format
9. **Unit test** at `tests/Unit/Question/<TypeName>QuestionTest.php`
   - Test: correct answer → full score
   - Test: wrong answer → zero/partial score
   - Test: wrong Answer type → `\InvalidArgumentException`
   - Test: VO invariants (empty, duplicate, etc.)

## Conventions to follow

- Domain layer: **zero** `use Illuminate\` or `use App\` imports
- VO validation always in the constructor — never in handlers or services
- Grading: all-or-nothing returns `new GradeResult(true/false, $this->score())` or `GradeResult::incorrect()`
- Partial credit: `($correct / $total) >= 0.5` → `isCorrect`, score = `($correct / $total) * $this->score->value()`
- `MatchingPairs` enforces unique left **and** unique right — don't reuse right values across pairs
- Seeder IDs must not conflict with existing rows (current: 1–7)
- Frontend: ordering/category items must be shuffled via `$question->display_items` set in `ExamTakeController`

## Existing question types for reference

| Type | Scoring | Answer format |
|---|---|---|
| `true_false` | all-or-nothing | `bool` |
| `single_choice` | all-or-nothing | `string` key |
| `multiple_choice` | all-or-nothing, normalized | `string[]` |
| `fill_blank` | all-or-nothing, case-insensitive | `string` |
| `matching` | partial ≥50% | `array<string,string>` |
| `ordering` | all-or-nothing | `string[]` ordered |
| `category` | partial ≥50% | `array<string,string>` |

## Run tests after

```bash
./vendor/bin/phpunit tests/Unit/Question/<TypeName>QuestionTest.php
```
