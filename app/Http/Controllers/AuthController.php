<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $instance = User::where('user_code', $request->username)->first();

        if ($instance) {
            if ($instance->isMatchPassword($request->password)) {
                $token = $instance->setAuthToken();
                return [
                    'userCode' => $instance->user_code,
                    'firstName' => $instance->first_name,
                    'lastName' => $instance->last_name,
                    'apiToken' => $token,
                    'role' => $instance->role,
                ];
            }
        }
        return response(null, 401);
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'password_old' => ['required'],
            'password' => ['required', 'confirmed'],
        ]);
        $instance = $request->user();
        if ($instance->isMatchPassword($request->passwordOld)) {
            $instance->password = $request->password;
            $instance->save();
            return [];
        }
        return response(null, 400);
    }
}
