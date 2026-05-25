<?php

namespace Tests\Unit\Question;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Testcenter\Domain\Question\Category\Categories;
use Testcenter\Domain\Question\Category\CategoryMap;
use Testcenter\Domain\Question\QuestionID;
use Testcenter\Domain\Question\QuestionText;
use Testcenter\Domain\Question\Type\CategoryQuestion;
use Testcenter\Domain\Shared\Score;
use Testcenter\Domain\Submission\Answer\CategoryAnswer;
use Testcenter\Domain\Submission\Answer\TrueFalseAnswer;

class CategoryQuestionTest extends TestCase
{
    private function makeQuestion(): CategoryQuestion
    {
        $categories = new Categories(['Animal', 'Plant', 'Mineral']);
        return new CategoryQuestion(
            id: new QuestionID(1),
            text: new QuestionText('Categorize each item'),
            score: new Score(10.0),
            categories: $categories,
            correctMap: new CategoryMap([
                'Dog'  => 'Animal',
                'Rose' => 'Plant',
                'Gold' => 'Mineral',
                'Cat'  => 'Animal',
            ], $categories),
        );
    }

    public function test_it_returns_full_score_when_all_items_are_correct(): void
    {
        $result = $this->makeQuestion()->grade(new CategoryAnswer([
            'Dog'  => 'Animal',
            'Rose' => 'Plant',
            'Gold' => 'Mineral',
            'Cat'  => 'Animal',
        ]));

        $this->assertTrue($result->isCorrect());
        $this->assertEquals(10.0, $result->score()->value());
    }

    public function test_it_returns_zero_score_when_all_items_are_wrong(): void
    {
        $result = $this->makeQuestion()->grade(new CategoryAnswer([
            'Dog'  => 'Plant',
            'Rose' => 'Mineral',
            'Gold' => 'Animal',
            'Cat'  => 'Plant',
        ]));

        $this->assertFalse($result->isCorrect());
        $this->assertEquals(0.0, $result->score()->value());
    }

    public function test_it_is_correct_when_exactly_half_are_correct(): void
    {
        $result = $this->makeQuestion()->grade(new CategoryAnswer([
            'Dog'  => 'Animal',  // correct
            'Rose' => 'Plant',   // correct
            'Gold' => 'Animal',  // wrong
            'Cat'  => 'Plant',   // wrong
        ]));

        $this->assertTrue($result->isCorrect());
        $this->assertEquals(5.0, $result->score()->value());
    }

    public function test_it_returns_incorrect_when_less_than_half_are_correct(): void
    {
        $result = $this->makeQuestion()->grade(new CategoryAnswer([
            'Dog'  => 'Animal',  // correct
            'Rose' => 'Mineral', // wrong
            'Gold' => 'Animal',  // wrong
            'Cat'  => 'Plant',   // wrong
        ]));

        $this->assertFalse($result->isCorrect());
        $this->assertEquals(2.5, $result->score()->value());
    }

    public function test_it_throws_exception_when_answer_type_is_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeQuestion()->grade(new TrueFalseAnswer(true));
    }

    public function test_categories_rejects_empty_list(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Categories([]);
    }

    public function test_categories_rejects_duplicates(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Categories(['Animal', 'Animal', 'Plant']);
    }

    public function test_category_map_rejects_empty_assignments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CategoryMap([], new Categories(['Animal']));
    }

    public function test_category_map_rejects_unknown_category(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CategoryMap(['Dog' => 'Mineral'], new Categories(['Animal', 'Plant']));
    }
}
