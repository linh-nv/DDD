<?php

namespace App\Models;

use App\Models\Concerns\HasBinaryUuid;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasBinaryUuid;

    protected $fillable = [
        'uuid',
        'exam_id',
        'user_id',
        'score',
        'status',
        'submitted_at',
    ];
}
