<?php

namespace App\Http\Controllers\ApiControllers\fcm_controllers;

use App\Http\Controllers\Controller;
use App\Services\FcmService as ServicesFcmService;
use Illuminate\Http\Request;


// used


class FcmNotificationController extends Controller
{

    protected $fcmService;

    public function __construct(ServicesFcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }



    public function notify(Request $request)
    {
        $token = $request->input('fcm_token');
        $title = 'New Notification';
        $body = 'You have a new message';

        // $fcmService = new FcmService();
        // $fcmService->sendNotification($title, $body, $token);
        $this->fcmService->sendNotification($title, $body, $token);

        return response()->json(['message' => 'Notification sent successfully']);
    }



}
