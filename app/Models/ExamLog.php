<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class ExamLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_code',
        'exam_id',
        'exam_result_id',
        'question_id',
        'question_choice_id',
        'question_type',
        'answer',
        'is_correct',
        'score'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function question()
    {
        return $this->belongsTo(Question::class)->withTrashed();
    }

    public function exam_event()
    {
        return $this->belongsTo(Exam::class)->withTrashed();
    }

    public function exam_result()
    {
        return $this->belongsTo(ExamResult::class)->withTrashed();
    }

    public function question_choice()
    {
        return $this->belongsTo(QuestionChoice::class)->withTrashed();
    }
}
