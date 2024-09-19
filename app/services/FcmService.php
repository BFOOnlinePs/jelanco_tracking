<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;

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
        // Create a notification
        $notification = Notification::create($title, $body);

        // Create a message that targets a specific token
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification);

        try {
            // Send the message via FCM
            $this->messaging->send($message);
            Log::info('Message sent successfully');
        } catch (NotFound $e) {
            // Handle the case where the token is not found
            Log::info('Token not found: ' . $e->getMessage());
            // Optionally, you might want to log the error or remove the invalid token
        } catch (InvalidArgument $e) {
            // Handle invalid argument errors
            Log::info('Invalid argument: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other types of errors
            Log::info('Error sending message: ' . $e->getMessage());
        }
    }
}
