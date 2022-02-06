<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionChoice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    public function getSubject(Request $request)
    {
        $subjects = Question::groupBy('subject')->get();
        $res = [];
        foreach ($subjects as $val) {
            $res[] = [
                'label' => $val->subject,
                'value' => $val->subject
            ];
        }
        return $res;
    }

    public function getQuestionGroup(Request $request)
    {
        $request->validate([
            'subject' => ['required'],
        ]);

        $questionGroups = Question::where('subject', $request->subject)->groupBy('question_group')->get();
        $res = [];
        foreach ($questionGroups as $val) {
            $res[] = [
                'label' => $val->question_group,
                'value' => $val->question_group
            ];
        }
        return $res;
    }

    public function index(Request $request)
    {
        $request->validate([
            'question_type' => [Rule::in(['choice', 'writing'])],
        ]);

        if ($request->has('question_type')) {
            return Question::where('question_type', $request->question_type)->get();
        }

        return Question::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_type' => ['required', Rule::in(['choice', 'writing'])],
            'subject' => ['required'],
            'question_group' => ['required'],
            'question_name' => ['required'],
            'question_answers' => ['required_if:question_type,choice', 'array'],
        ]);


        $question = Question::create([
            'name' => $request->question_name,
            'subject' => $request->subject['value'],
            'question_type' => $request->question_type,
            'question_group' => $request->question_group['value'],
        ]);

        foreach ($request->input('question_answers', []) as $index => $answer) {
            if ($answer) {
                QuestionChoice::create([
                    'name' => $answer,
                    'is_correct' => $index === 0,
                    'question_id' => $question->_id
                ]);
            }
        }

        return response(null, 201);
    }

    public function show($id)
    {
        return Question::with('choices')->find($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question_type' => ['required', Rule::in(['choice', 'writing'])],
            'subject' => ['required'],
            'question_group' => ['required'],
            'question_name' => ['required'],
            'question_answers' => ['required_if:question_type,choice', 'array'],
        ]);

        $question = Question::find($id);
        $question->name = $request->question_name;
        $question->subject = $request->subject['value'];
        $question->question_group = $request->question_group['value'];
        $question->save();

        foreach ($request->input('question_answers', []) as $index => $answer) {
            if ($answer) {
                if (!in_array($index, [2, 3, 4])) {
                    $question->choices()->where('_id', $index)->update(['name' => $answer]);
                } else {
                    QuestionChoice::create([
                        'name' => $answer,
                        'is_correct' => false,
                        'question_id' => $question->_id
                    ]);
                }
            }
        }
        return [];
    }

    public function destroy($id)
    {
        Question::where('_id', $id)->delete();;
        return [];
    }
}
