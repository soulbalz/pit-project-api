<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function getStudent(Request $request)
    {
        $students = User::where('role', 'student')->get();
        $res = [];
        foreach ($students as $val) {
            $res[] = [
                'label' => "{$val->user_code} - {$val->first_name} {$val->last_name}",
                'value' => $val->user_code
            ];
        }
        return $res;
    }

    public function index(Request $request)
    {
        return User::where('role', 'student')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_code' => ['required', 'unique:users'],
            'password' => ['required', 'confirmed'],
            'first_name' => ['required'],
            'last_name' => ['required'],
        ]);

        User::create([
            'user_code' => $request->user_code,
            'password' => $request->password,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => 'student',
        ]);
        return response(null, 201);
    }

    public function show($id)
    {

        return User::where([['_id', $id], ['role', 'student']])->firstOrFail();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
        ]);

        $user = User::where([['_id', $id], ['role', 'student']])->firstOrFail();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->save();
        return [];
    }

    public function destroy($id)
    {
        User::where([['_id', $id], ['role', 'student']])->delete();
        return [];
    }

    public function changePassword(Request $request, $id): array
    {
        $request->validate([
            'password' => ['required', 'confirmed'],
        ]);
        $user = User::where([['_id', $id], ['role', 'student']])->firstOrFail();
        $user->password = $request->password;
        $user->save();
        return [];
    }
}
