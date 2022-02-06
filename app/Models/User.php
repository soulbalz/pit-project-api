<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_code',
        'password',
        'first_name',
        'last_name',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function setAuthToken(): string
    {
        $token = Str::random(60);
        $this->api_token = hash('sha256', $token);
        $this->save();
        return $token;
    }

    public function isMatchPassword($password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function exam_results()
    {
        return $this->hasMany(ExamResult::class)->withTrashed();
    }

    public function exam_logs()
    {
        return $this->hasMany(ExamLog::class)->withTrashed();
    }
}
