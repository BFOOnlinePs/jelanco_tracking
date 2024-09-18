<?php

namespace App\Http\Controllers\ApiControllers\fcm_controllers;

use App\Http\Controllers\Controller;
use App\Models\FCMRegistrationTokens;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FcmController extends Controller
{
    public function storeFcmUserToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'frt_user_id' => 'required',
            'frt_registration_token' => 'required',
        ], [
            'frt_user_id.required' => 'الرجاء ارسال رقم المستخدم',
            'frt_registration_token.required' => 'الرجاء ارسال التوكين المراد تخزينه',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $fcmUserToken = FCMRegistrationTokens::create([
            'frt_user_id' => $request->input('frt_user_id'),
            'frt_registration_token' => $request->input('frt_registration_token'),
            'frt_date' =>  Carbon::now()->format('Y-m-d'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تخزين التوكين',
            'data' => $fcmUserToken
        ], 200);
    }

    public function updateFcmUserToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'frt_user_id' => 'required',
            'frt_old_registration_token' => 'required',
            'frt_new_registration_token' => 'required',
        ], [
            'frt_user_id.required' => 'الرجاء ارسال رقم المستخدم',
            'frt_old_registration_token.required' => 'الرجاء ارسال التوكين القديم',
            'frt_new_registration_token.required' => 'الرجاء ارسال التوكين الجديد',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $fcmUserToken = FCMRegistrationTokens::where('frt_user_id', $request->input('frt_user_id'))
            ->where('frt_registration_token', $request->input('frt_old_registration_token'))->first();

        if (!$fcmUserToken) {
            return response()->json([
                'status' => false,
                'message' => 'التوكين غير موجود',
            ], 400);
        }

        $fcmUserToken->update([
            'frt_registration_token' => $request->input('frt_new_registration_token')
        ]);


        return response()->json([
            'status' => true,
            'message' => 'تم تحديث التوكين',
            // 'data' => $fcmUserToken
        ], 200);
    }

    public function deleteFcmUserToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'frt_user_id' => 'required',
            'frt_registration_token' => 'required',
        ], [
            'frt_user_id.required' => 'الرجاء ارسال رقم المستخدم',
            'frt_registration_token.required' => 'الرجاء ارسال التوكين المراد حذفه',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $fcmUserToken = FCMRegistrationTokens::where('frt_user_id', $request->input('frt_user_id'))
            ->where('frt_registration_token', $request->input('frt_registration_token'))->get();


        if ($fcmUserToken->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'التوكين غير موجود',
            ], 400);
        }

        // if by accident saved the same token more than one time
        foreach ($fcmUserToken as $token) {
            $token->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم حذف التوكين',
            // 'token' =>$fcmUserToken
        ], 200);
    }
}
