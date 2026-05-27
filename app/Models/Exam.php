<?php

namespace App\Models;

use App\Models\Concerns\HasBinaryUuid;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasBinaryUuid;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Question::class,
            'exam_questions',
            'exam_id',     // FK on pivot referencing exams.uuid
            'question_id'  // FK on pivot referencing questions.uuid
        )
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function submissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Submission::class, 'exam_id', 'uuid');
    }
}
