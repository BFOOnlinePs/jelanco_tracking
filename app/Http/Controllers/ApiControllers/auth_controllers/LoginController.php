<?php

namespace App\Http\Controllers\ApiControllers\auth_controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ], [
            'email.required' => 'الرجاء إرسال الايميل',
            'password.required' => 'الرجاء إرسال كلمة المرور',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $credentials = $validator->validated();

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('api-token')->plainTextToken;
            $user = User::find(auth()->user()->id);
            // $role = $user->getRoleNames()->first();
            // $permissions = $user->getAllPermissions();

            return response([
                'status' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => auth()->user(),
                'token' => $token,
                // 'role' => $role,
                // 'permissions' => $permissions
            ], 200);
        } else {
            return response([
                'status' => false,
                'message' => 'الرجاء التأكد من البيانات المدخلة'
            ], 401);
        }
    }
}
