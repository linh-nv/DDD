<?php

namespace App\Models;

use App\Models\Concerns\HasBinaryUuid;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasBinaryUuid;

    protected $fillable = [
        'uuid',
        'type',
        'content',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'payload',
        'score',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
