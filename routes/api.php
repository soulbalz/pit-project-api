<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth', [\App\Http\Controllers\AuthController::class, 'authenticate']);
Route::post('/register', [\App\Http\Controllers\StudentController::class, 'store']);
Route::post('/register/teacher', [\App\Http\Controllers\UserController::class, 'store']);
Route::post('/mock', [\App\Http\Controllers\UserController::class, 'createMockUser']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\UserController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\UserController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\UserController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);
        Route::put('/{id}/changepassword', [\App\Http\Controllers\UserController::class, 'changePassword']);
    });

    Route::prefix('students')->group(function () {
        Route::get('/', [\App\Http\Controllers\StudentController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\StudentController::class, 'store']);
        Route::get('/options', [\App\Http\Controllers\StudentController::class, 'getStudent']);
        Route::get('/{id}', [\App\Http\Controllers\StudentController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\StudentController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\StudentController::class, 'destroy']);
        Route::put('/{id}/changepassword', [\App\Http\Controllers\StudentController::class, 'changePassword']);
    });

    Route::prefix('questions')->group(function () {
        Route::get('/', [\App\Http\Controllers\QuestionController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\QuestionController::class, 'store']);
        Route::get('/subjects', [\App\Http\Controllers\QuestionController::class, 'getSubject']);
        Route::get('/groups', [\App\Http\Controllers\QuestionController::class, 'getQuestionGroup']);
        Route::get('/{id}', [\App\Http\Controllers\QuestionController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\QuestionController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\QuestionController::class, 'destroy']);
    });

    Route::prefix('exams')->group(function () {
        Route::get('/', [\App\Http\Controllers\ExamController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\ExamController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\ExamController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\ExamController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\ExamController::class, 'destroy']);
    });

    Route::prefix('examinations')->group(function () {
        Route::get('/', [\App\Http\Controllers\ExaminationController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\ExaminationController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\ExaminationController::class, 'update']);
        Route::get('/{id}/review', [\App\Http\Controllers\ExaminationController::class, 'reviewAnswer']);
        Route::put('/{id}/review', [\App\Http\Controllers\ExaminationController::class, 'saveReviewAnswer']);
    });
});
