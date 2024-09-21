<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;


// used


class FcmService
{
    protected $messaging;

    public function __construct()
    {
        $firebase = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
        $this->messaging = $firebase->createMessaging();
    }

    // if type is 'task', then id is 'task id'
    public function sendNotification($title, $body, $token,  $type = null, $type_id = null)
    {
        // Create a notification
        $notification = Notification::create($title, $body);

        // Create a message that targets a specific token
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification);


        // Prepare data payload
        $data = [];

        if ($type !== null) {
            $data[config('constants.notification.type')] = $type;
        }

        if ($type_id !== null) {
            $data[config('constants.notification.type_id')] = $type_id;
        }

        // Attach the data payload if it exists
        if (!empty($data)) {
            $message = $message->withData($data);
        }

        try {
            // Send the message via FCM
            $this->messaging->send($message);

            Log::info('Message sent successfully');
        } catch (NotFound $e) {
            Log::info('Token not found: ' . $e->getMessage());
        } catch (InvalidArgument $e) {
            Log::info('Invalid argument: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::info('Error sending message: ' . $e->getMessage());
        }
    }
}
