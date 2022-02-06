<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class QuestionChoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'is_correct',
        'question_id',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class)->withTrashed();
    }

    public function exam_logs()
    {
        return $this->hasMany(ExamLog::class)->withTrashed();
    }
}
