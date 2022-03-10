<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamLog;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\QuestionChoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExaminationController extends Controller
{
    public function index(Request $request)
    {
        return ExamResult::with('exam')->where('user_code', $request->user()->user_code)->get();
    }

    public function show($id)
    {
        $examResult = ExamResult::with('exam')->find($id);
        $exam = $examResult->exam;

        if (!Carbon::now()->between($exam->exam_start, $exam->exam_end) || $examResult->is_submited) abort(404);

        $questionChoices = Question::with('choices')
            ->where('subject', $exam->subject)
            ->where('question_group', $exam->question_choice_group)
            ->where('question_type', 'choice')
            ->get()
            ->random($exam->total_question_choice);

        $questionWritings = Question::where('subject', $exam->subject)
            ->where('question_group', $exam->question_writing_group)
            ->where('question_type', 'writing')
            ->get()
            ->random($exam->total_question_writing);

        $examLogs = [];
        foreach ($questionChoices as $questionChoice) {
            $examLogs[] = [
                'user_code' => $examResult->user_code,
                'exam_id' => $exam->_id,
                'exam_result_id' => $examResult->_id,
                'question_id' => $questionChoice->_id,
                'answer' => null,
                'question_choice_id' => null,
                'is_correct' => false,
                'question_type' => 'choice'
            ];
        }
        foreach ($questionWritings as $questionWriting) {
            $examLogs[] = [
                'user_code' => $examResult->user_code,
                'exam_id' => $exam->_id,
                'exam_result_id' => $examResult->_id,
                'question_id' => $questionWriting->_id,
                'answer' => null,
                'score' => 0,
                'question_type' => 'writing'
            ];
        }
        ExamLog::where('user_code', $examResult->user_code)->delete();
        ExamLog::insert($examLogs);

        $responseData = $examResult->toArray();
        $responseData['exam_time'] = $exam->exam_time;
        $responseData['question_choices'] = $questionChoices;
        $responseData['question_writings'] = $questionWritings;
        return $responseData;
    }

    public function update(Request $request, $id)
    {
        $examResult = ExamResult::with('exam')->find($id);
        $exam = $examResult->exam;

        $totalQuestionChoice = $exam->total_question_choice;
        $totalQuestionWriting = $exam->total_question_writing;

        $rules = [
            'total_copy_paste' => 'numeric',
            'total_switch_screen' => 'numeric'
        ];
        if ($totalQuestionChoice > 0) {
            $rules['choices'] = 'required|array|max:'. $totalQuestionChoice;
            $rules['choices.*'] = 'required';
        }
        if ($totalQuestionWriting > 0) {
            $rules['writings'] = 'required|array|max:'. $totalQuestionWriting;
            $rules['writings.*'] = 'required';
        }

        $request->validate($rules);

        $examLogs = ExamLog::where('exam_result_id', $id)->get();
        $choiceAnswers = [];
        foreach ($examLogs as $examLog) {
            if ($examLog->question_type == 'choice' && key_exists($examLog->question_id, $request->choices)) {
                $choiceAnswers[] = $request->choices[$examLog->question_id];

            }
        }

        $writingAnswers = [];
        foreach ($examLogs as $examLog) {
            if ($examLog->question_type == 'writing' && key_exists($examLog->question_id, $request->writings)) {
                $writingAnswers[$examLog->question_id] = $request->writings[$examLog->question_id];

            }
        }
        $totalCorrectAnswer = QuestionChoice::whereIn('_id', $choiceAnswers)->where('is_correct', true)->count();

        $examResult->total_copy_paste = (int)$request->input('total_copy_paste', 0);
        $examResult->total_switch_screen = (int)$request->input('total_switch_screen', 0);
        $examResult->total_choice_pass = $totalCorrectAnswer;
        $examResult->total_choice_fail = $totalQuestionChoice - $totalCorrectAnswer;
        $examResult->is_submited = true;
        $examResult->save();

        $questionChoices = QuestionChoice::whereIn('_id', $choiceAnswers)->get();
        foreach ($questionChoices as $questionChoice) {
            ExamLog::where('exam_result_id', $id)
                ->where('question_id', $questionChoice->question_id)
                ->update([
                    'answer' => $questionChoice->name,
                    'question_choice_id' => $questionChoice->_id,
                    'is_correct' => $questionChoice->is_correct,
                ]);
        }

        foreach ($writingAnswers as $questionId => $answer) {
            ExamLog::where('exam_result_id', $id)
                ->where('question_id', $questionId)
                ->update([
                    'answer' => $answer
                ]);
        }
        return [];
    }

    public function reviewAnswer($id)
    {
        $examResult = ExamResult::with('exam', 'user')->find($id);
        $exam = $examResult->exam;
        $examLogs = ExamLog::with('question')
            ->where('exam_result_id', $id)
            ->where('question_type', 'writing')
            ->get();

        $responseData = $examResult->toArray();
        $responseData['exam_time'] = $exam->exam_time;
        $responseData['exam_logs'] = $examLogs;
        return $responseData;
    }

    public function saveReviewAnswer(Request $request, $id)
    {
        $examResult = ExamResult::with('exam')->find($id);
        $exam = $examResult->exam;

        $totalQuestionWriting = $exam->total_question_writing;

        $request->validate([
            'scores' => 'required|array|max:'. $totalQuestionWriting,
            'scores.*' => 'required|numeric'
        ]);

        $examLogs = ExamLog::where('exam_result_id', $id)
            ->where('question_type', 'writing')
            ->get();

        $totalScore = 0;
        foreach ($examLogs as $examLog) {
            if (key_exists($examLog->question_id, $request->scores)) {
                $score = (float)$request->scores[$examLog->question_id];
                ExamLog::where('exam_result_id', $id)
                    ->where('question_id', $examLog->question_id)
                    ->update([
                        'score' => (float)number_format($score, '2', '.', '')
                    ]);
                $totalScore += $score;
            }
        }

        $examResult->total_writing_score = (float)number_format($totalScore, '2', '.', '');
        $examResult->save();

        return [];
    }
}
