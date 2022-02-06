<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class ExamResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_timer',
        'total_writing_score',
        'total_choice_pass',
        'total_choice_fail',
        'is_submited',
        'user_code',
        'exam_id',
        'exam_at',
        'total_copy_paste',
        'total_switch_screen'
    ];

    protected $casts = [
        'is_submited' => 'boolean',
        'total_writing_score' => 'int',
        'total_choice_pass' => 'int',
        'total_choice_fail' => 'int',
        'total_copy_paste' => 'int',
        'total_switch_screen' => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'user_code');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class)->withTrashed();
    }

    public function exam_logs()
    {
        return $this->hasMany(ExamLog::class)->withTrashed();
    }
}
