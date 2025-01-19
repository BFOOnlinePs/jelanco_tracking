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
            'email_phone' => 'required',
            'password' => 'required'
        ], [
            'email_phone.required' => 'الرجاء إرسال الايميل او رقم الجوال',
            'password.required' => 'الرجاء إرسال كلمة المرور',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $credentials = $validator->validated();
        $emailOrPhone = $credentials['email_phone'];
        $password = $credentials['password'];

        // Determine if input is email or phone number
        if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $emailOrPhone, 'password' => $password];
        } else {
            $credentials = ['phone_number' => $emailOrPhone, 'password' => $password];
        }


        if (Auth::attempt($credentials)) {
            // Check if user is active
            if (Auth::user()->user_status != 'active') {
                return response()->json([
                    'status' => false,
                    'message' => 'حسابك غير مفعل حالياً. إذا كنت بحاجة للمساعدة يُرجى التواصل مع المسؤول.'
                ], 403);
            }

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
