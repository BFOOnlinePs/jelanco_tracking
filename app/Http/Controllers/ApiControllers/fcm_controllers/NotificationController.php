<?php

namespace App\Http\Controllers\ApiControllers\fcm_controllers;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationController extends Controller
{
    protected $firebaseService;
    protected $notification;


    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
        $this->notification = Firebase::messaging();

    }

    public function sendNotification(Request $request)
    {
        $path = env('FIREBASE_CREDENTIALS');
        // if (file_exists($path)) {
        //     return "File exists and is readable.";
        // } else {
        //     return "File does not exist or is not readable.";
        // }
        $validated = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'token' => 'required|string',
        ]);

        $title = $request->input('title');
        $body = $request->input('body');
        $token = $request->input('token');

        try {
            $this->firebaseService->sendNotification($title, $body, $token);
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // $this->firebaseService->sendNotification($title, $body, $token);

        // return response()->json(['message' => 'Notification sent successfully']);
    }




    //


    public function notification(Request $request)
    {

        $fcmToken = 'ccCT1sSoQVS8c-Nz9ciZyR:APA91bHnfTDa91A1gZQ9C-rYAEQ7_mQzMl6l03x4Q1D9da49ltCkNm3cZh5e0Fw5H9u5COG_cDBmiiO49zZOoR6HDrF91dWPshMQ5qNZG4czYpLsx26OG9nAtQ9quGygoao19nUl2mEV';
        // $fcmToken = auth()->user()->fcm_token;

        $title = $request->input('title');

        $body = $request->input('body');

        $message = CloudMessage::fromArray([

            'token' => $fcmToken,

            'notification' => [

                'title' => $title,

                'body' => $body

            ],

        ]);

        $this->notification->send($message);
    }
}
