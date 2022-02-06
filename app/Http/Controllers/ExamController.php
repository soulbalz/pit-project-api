<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Question;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        return Exam::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_name' => ['required'],
            'subject' => ['required'],
            'question_choice_group' => ['required_without:question_writing_group'],
            'total_question_choice' => ['required_with:question_choice_group', 'numeric'],
            'question_writing_group' => ['required_without:question_choice_group'],
            'total_question_writing' => ['required_with:question_writing_group', 'numeric'],
            'exam_start' => ['required', 'date'],
            'exam_end' => ['required', 'date'],
            'exam_time' => ['required', 'numeric'],
            'user_codes' => ['required', 'array'],
        ]);

        $exam = Exam::create([
            'name' => $request->exam_name,
            'detail' => $request->input('exam_detail'),
            'subject' => $request->subject,
            'exam_start' => $request->exam_start,
            'exam_end' => $request->exam_end,
            'exam_time' => $request->exam_time,
            'question_choice_group' => $request->input('question_choice_group'),
            'total_question_choice' => $request->input('total_question_choice', 0),
            'question_writing_group' => $request->input('question_writing_group'),
            'total_question_writing' => $request->input('total_question_writing', 0),
        ]);

        foreach ($request->user_codes as $userCode) {
            ExamResult::create([
                'user_code' => $userCode,
                'exam_id' => $exam->_id,
            ]);
        }

        return response(null, 201);
    }

    public function show($id)
    {
        return Exam::with('exam_results', 'exam_results.user')->find($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'exam_name' => ['required'],
            'subject' => ['required'],
            'question_choice_group' => ['required_without:question_writing_group'],
            'total_question_choice' => ['required_with:question_choice_group', 'numeric'],
            'question_writing_group' => ['required_without:question_choice_group'],
            'total_question_writing' => ['required_with:question_writing_group', 'numeric'],
            'exam_start' => ['required', 'date'],
            'exam_end' => ['required', 'date'],
            'exam_time' => ['required', 'numeric'],
        ]);

        $exam = Exam::find($id);
        $exam->name = $request->exam_name;
        $exam->detail = $request->exam_detail;
        $exam->exam_start = $request->exam_start;
        $exam->exam_end = $request->exam_end;
        $exam->question_choice_group = $request->input('question_choice_group');
        $exam->total_question_choice = $request->input('total_question_choice', 0);
        $exam->question_writing_group = $request->input('question_writing_group');
        $exam->total_question_writing = $request->input('total_question_writing', 0);
        $exam->save();

        return [];
    }

    public function destroy($id)
    {
        Question::where('_id', $id)->delete();;
        return [];
    }
}
