<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    protected $messaging;

    public function __construct()
    {
        $firebase = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
        $this->messaging = $firebase->createMessaging();
    }

    public function sendNotification($title, $body, $token)
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body));

        $this->messaging->send($message);


        // // Create a notification
        // $notification = Notification::create($title, $body);

        // // Create a message that targets a specific token
        // $message = CloudMessage::withTarget('token', $token)
        //     ->withNotification($notification);

        // // Send the message via FCM
        // $this->messaging->send($message);
    }
}
