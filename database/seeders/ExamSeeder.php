<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamSeeder extends Seeder
{
    public const EXAM1 = 'b0000000-0000-0000-0000-000000000001';

    private static function bin(string $uuid): string
    {
        return hex2bin(str_replace('-', '', $uuid));
    }

    public function run(): void
    {
        DB::table('exams')->insert([
            [
                'uuid'             => self::bin(self::EXAM1),
                'title'            => 'PHP Backend Basic Test',
                'description'      => 'Bài test cơ bản dành cho backend developer.',
                'duration_minutes' => 30,
                'started_at'       => now(),
                'ended_at'         => now()->addDays(30),
                'is_active'        => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);

        DB::table('exam_questions')->insert([
            ['exam_id' => self::bin(self::EXAM1), 'question_id' => self::bin(QuestionSeeder::Q1), 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => self::bin(self::EXAM1), 'question_id' => self::bin(QuestionSeeder::Q2), 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => self::bin(self::EXAM1), 'question_id' => self::bin(QuestionSeeder::Q3), 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => self::bin(self::EXAM1), 'question_id' => self::bin(QuestionSeeder::Q4), 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => self::bin(self::EXAM1), 'question_id' => self::bin(QuestionSeeder::Q5), 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => self::bin(self::EXAM1), 'question_id' => self::bin(QuestionSeeder::Q6), 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => self::bin(self::EXAM1), 'question_id' => self::bin(QuestionSeeder::Q7), 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
