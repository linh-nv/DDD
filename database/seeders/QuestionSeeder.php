<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('questions')->insert([
            [
                'id' => 1,
                'type' => 'true_false',
                'content' => 'PHP là ngôn ngữ strongly typed.',

                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,

                'correct_answer' => '0',

                'payload' => json_encode([
                    'correct' => false,
                ]),

                'score' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'type' => 'single_choice',
                'content' => 'Laravel được viết bằng ngôn ngữ nào?',

                'option_a' => 'Java',
                'option_b' => 'PHP',
                'option_c' => 'Go',
                'option_d' => 'Rust',

                'correct_answer' => 'B',

                'payload' => json_encode([
                    'options' => [
                        'A' => 'Java',
                        'B' => 'PHP',
                        'C' => 'Go',
                        'D' => 'Rust',
                    ],
                    'correct' => 'B',
                ]),

                'score' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'type' => 'fill_blank',
                'content' => 'Framework phổ biến của PHP là ________.',

                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,

                'correct_answer' => 'laravel',

                'payload' => json_encode([
                    'answers' => [
                        'laravel',
                        'Laravel',
                    ],
                ]),

                'score' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'type' => 'matching',

                'content' => 'Match technologies with purpose',

                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,

                'correct_answer' => null,

                'payload' => json_encode([
                    'pairs' => [
                        'PHP' => 'Backend',
                        'Vue' => 'Frontend',
                        'Redis' => 'Cache',
                    ],
                ]),

                'score' => 3,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'type' => 'multiple_choice',
                'content' => 'Chọn các framework PHP phổ biến.',

                'option_a' => 'Laravel',
                'option_b' => 'Symfony',
                'option_c' => 'Django',
                'option_d' => 'CodeIgniter',

                'correct_answer' => null,

                'payload' => json_encode([
                    'options' => [
                        'A' => 'Laravel',
                        'B' => 'Symfony',
                        'C' => 'Django',
                        'D' => 'CodeIgniter',
                    ],
                    'correct' => ['A', 'B', 'D'],
                ]),

                'score' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'type' => 'ordering',
                'content' => 'Sắp xếp các bước xử lý HTTP request trong Laravel theo đúng thứ tự.',

                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,

                'correct_answer' => null,

                'payload' => json_encode([
                    'correct_order' => [
                        'Request',
                        'Kernel',
                        'Middleware',
                        'Router',
                        'Controller',
                        'Response',
                    ],
                ]),

                'score' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'type' => 'category',
                'content' => 'Phân loại các công nghệ sau theo tầng kiến trúc.',

                'option_a' => null,
                'option_b' => null,
                'option_c' => null,
                'option_d' => null,

                'correct_answer' => null,

                'payload' => json_encode([
                    'categories' => ['Frontend', 'Backend', 'Database'],
                    'correct_map' => [
                        'Vue'     => 'Frontend',
                        'React'   => 'Frontend',
                        'Laravel' => 'Backend',
                        'MySQL'   => 'Database',
                        'Redis'   => 'Database',
                    ],
                ]),

                'score' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
