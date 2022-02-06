<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Will be remove
    public function createMockUser(Request $request)
    {
        if (!User::where('user_code', 'admin')->exists()) {
            User::create([
                'user_code' => 'admin',
                'password' => 'password',
                'first_name' => 'firstname',
                'last_name' => 'lastname',
                'role' => 'admin',
            ]);
        }
        return response(null, 201);
    }

    public function index(Request $request)
    {
        return User::whereIn('role', ['teacher', 'admin'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_code' => ['required', 'unique:users'],
            'password' => ['required', 'confirmed'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'role' => [Rule::in(['teacher', 'admin'])],
        ]);

        User::create([
            'user_code' => $request->user_code,
            'password' => $request->password,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => $request->input('role', 'teacher'),
        ]);
        return response(null, 201);
    }

    public function show($id)
    {
        return User::where('_id', $id)->whereIn('role', ['teacher', 'admin'])->firstOrFail();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_code' => ['required', 'unique:users,user_code,' . $id],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'role' => [Rule::in(['teacher', 'admin'])],
        ]);

        $user = User::where('_id', $id)->whereIn('role', ['teacher', 'admin'])->firstOrFail();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->role = $request->input('role', 'teacher');
        $user->save();
        return [];
    }

    public function destroy($id)
    {
        User::where('_id', $id)->whereIn('role', ['teacher', 'admin'])->delete();;
        return [];
    }

    public function changePassword(Request $request, $id): array
    {
        $request->validate([
            'password' => ['required', 'confirmed'],
        ]);
        $user = User::where('_id', $id)->whereIn('role', ['teacher', 'admin'])->firstOrFail();
        $user->password = $request->password;
        $user->save();
        return [];
    }
}
