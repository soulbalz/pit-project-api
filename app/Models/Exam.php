<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'detail',
        'subject',
        'exam_start',
        'exam_end',
        'exam_time',
        'question_choice_group',
        'total_question_choice',
        'question_writing_group',
        'total_question_writing',
    ];

    protected $dates = [
        'exam_start',
        'exam_end',
    ];

    protected $casts = [
        'exam_time' => 'int',
        'total_question_choice' => 'int',
        'total_question_writing' => 'int',
    ];

    public function exam_results() {
        return $this->hasMany(ExamResult::class)->withTrashed();
    }

    public function exam_logs()
    {
        return $this->hasMany(ExamLog::class)->withTrashed();
    }
}
