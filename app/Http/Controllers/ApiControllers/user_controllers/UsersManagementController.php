<?php

namespace App\Http\Controllers\ApiControllers\user_controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersManagementController extends Controller
{
    protected $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }


    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|unique:users,phone_number',
            'password' => 'required',
            'name' => 'required',
            'departments' => 'nullable|json',
            'status' => 'required|in:active,not_active'
        ], [
            'email.unique' => 'البريد الإلكتروني موجود بالفعل',
            'phone.unique' => 'رقم الجوال موجود بالفعل',
            'email.required_without' => 'الرجاء كتابة البريد الإلكتروني او رقم الجوال',
            'phone.required_without' => 'الرجاء كتابة البريد الإلكتروني او رقم الجوال',
            'password.required' => 'الرجاء كتابة كلمة المرور',
            'name.required' => 'الرجاء كتابة الاسم',
            'status.required' => 'الرجاء كتابة حالة الموظف',
            'status.in' => 'الرجاء كتابة حالة الموظف بشكل صحيح'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if (!$request->has('email') && !$request->has('phone')) {
            return response()->json([
                'status' => false,
                'message' => 'الرجاء كتابة البريد الإلكتروني او رقم الجوال'
            ], 422);
        }

        $user = new User();
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone');
        $user->password = bcrypt($request->input('password'));
        $user->name = $request->input('name');
        $user->job_title = $request->input('job_title');
        $user->departments = $request->input('departments');
        $user->user_status = $request->input('status');

        if ($user->save()) {
            return response([
                'status' => true,
                'message' => 'تم إضافة الموظف بنجاح',
                'user' => $user,
            ], 200);
        }
    }


    /// todo: send the user_status
    public function updateUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:users,email,' . $id,
            'phone' => 'nullable|unique:users,phone_number,' . $id,
            // 'password' => 'required',
            'name' => 'required',
            'departments' => 'nullable|json',
            'status' => 'required|in:active,not_active'
        ], [
            'email.unique' => 'البريد الإلكتروني موجود بالفعل',
            'phone.unique' => 'رقم الجوال موجود بالفعل',
            'email.required_without' => 'الرجاء كتابة البريد الإلكتروني او رقم الجوال',
            'phone.required_without' => 'الرجاء كتابة البريد الإلكتروني او رقم الجوال',
            // 'password.required' => 'الرجاء كتابة كلمة المرور',
            'name.required' => 'الرجاء كتابة الاسم',
            'status.required' => 'الرجاء كتابة حالة الموظف',
            'status.in' => 'الرجاء كتابة حالة الموظف بشكل صحيح'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if (!$request->has('email') && !$request->has('phone')) {
            return response()->json([
                'status' => false,
                'message' => 'الرجاء كتابة البريد الإلكتروني او رقم الجوال'
            ], 422);
        }

        $user = User::find($id);
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone');
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->name = $request->input('name');
        $user->job_title = $request->input('job_title');
        $user->departments = $request->input('departments');
        $user->user_status = $request->input('status');

        // if status is not active, then remove the token
        if ($user->user_status != 'active') {
           // auth service to removeTokens
            $this->authService->removeTokens($user);
        }

        if ($user->save()) {
            return response([
                'status' => true,
                'message' => 'تم تحديث بيانات الموظف بنجاح',
                'user' => $user,
            ], 200);
        }
    }
}
