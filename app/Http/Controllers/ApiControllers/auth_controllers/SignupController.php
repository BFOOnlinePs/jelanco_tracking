<?php

namespace App\Http\Controllers\ApiControllers\auth_controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SignupController extends Controller
{
    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required', // confirmed in front end
        ],);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = new User();
        $user->email = $request->input('email');
        $user->password =  bcrypt($request->input('password'));
        $user->name = "Aseel";
        // $user->u_role_id = 1;

        $user->save();

        $token = $user->createToken('api-token')->plainTextToken;

        return response([
            'status' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}
