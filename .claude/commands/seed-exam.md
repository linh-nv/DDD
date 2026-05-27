# Seed the Exam Database

Reset and re-seed the database for the TestCenter project.

## Steps

```bash
php artisan migrate:fresh --seed
```

This runs all migrations and seeds in order:
1. `QuestionSeeder` — inserts questions 1–7 (all types)
2. `ExamSeeder` — inserts exam 1 and links all questions via `exam_questions`

## Question payload reference

| ID | Type | Payload keys |
|---|---|---|
| 1 | `true_false` | `correct: bool` |
| 2 | `single_choice` | `options: {key: label}`, `correct: string` |
| 3 | `fill_blank` | `answers: string[]` |
| 4 | `matching` | `pairs: {left: right}` — right values must be **unique** |
| 5 | `multiple_choice` | `options: {key: label}`, `correct: string[]` |
| 6 | `ordering` | `correct_order: string[]` |
| 7 | `category` | `categories: string[]`, `correct_map: {item: category}` |

## After seeding, verify via

```bash
php artisan tinker --execute="echo App\Models\Exam::with('questions')->find(1)->questions->count();"
```

Should output `7`.

## Test the API

```bash
# All correct answers — should return score 15
curl -s -X POST http://ddd.local/api/exams/submit-ddd \
  -H "Content-Type: application/json" \
  -d '{
    "exam_id": 1,
    "answers": {
      "1": false,
      "2": "B",
      "3": "laravel",
      "4": {"PHP":"Backend","Vue":"Frontend","Redis":"Cache"},
      "5": ["A","B","D"],
      "6": ["Request","Kernel","Middleware","Router","Controller","Response"],
      "7": {"Vue":"Frontend","React":"Frontend","Laravel":"Backend","MySQL":"Database","Redis":"Database"}
    }
  }' | python3 -m json.tool
```
