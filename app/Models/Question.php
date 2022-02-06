<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'question_type',
        'question_group',
    ];

    public function choices() {
        return $this->hasMany(QuestionChoice::class);
    }

    public function exam_logs()
    {
        return $this->hasMany(ExamLog::class)->withTrashed();
    }
}
