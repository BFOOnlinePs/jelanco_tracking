<?php

namespace App\Http\Controllers\ApiControllers\auth_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
        } catch (\Exception $e) {
        }

        return response([
            'status' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ], 200);
    }
}
