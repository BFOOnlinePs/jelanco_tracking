<?php

namespace App\Services;

use App\Models\FCMRegistrationTokens;
use App\Models\NotificationModel;
use GPBMetadata\Google\Api\Auth;
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


    public function sendNotification($title, $body, $userIds, $type = null, $type_id = null)
    {
        // Retrieve tokens for the users
        $tokensByUser = FCMRegistrationTokens::whereIn('frt_user_id', $userIds)
            ->get()
            ->groupBy('frt_user_id');

        // Iterate through each user
        foreach ($userIds as $userId) {
            // Save the notification once for each user
            NotificationModel::create([
                'user_id' => $userId,  // Associate the notification with the correct user
                'title' => $title,
                'body' => $body,
                'type' => $type,
                'type_id' => $type_id,
            ]);

            // Get all tokens for the user (if they exist)
            $userTokens = $tokensByUser->get($userId, collect());

            // If the user has tokens, send the notification to all their devices
            if ($userTokens->isNotEmpty()) {
                foreach ($userTokens as $token) {
                    $this->sendToToken($title, $body, $userId, $token->frt_registration_token, $type, $type_id);
                }
            }
        }
    }



    /**
     * Helper function to send a notification to a specific token.
     */
    private function sendToToken($title, $body, $userId, $token, $type, $type_id)
    {
        // Create a notification object for FCM
        $notification = Notification::create($title, $body);

        // Create a message targeting a specific token
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification);

        $data = [];

        if ($type !== null) {
            $data[config('constants.notification.type')] = $type;
        }

        if ($type_id !== null) {
            $data[config('constants.notification.type_id')] = $type_id;
        }

        // Attach data payload if it exists
        if (!empty($data)) {
            $message = $message->withData($data);
        }

        try {
            // Send the message via FCM
            $this->messaging->send($message);
            Log::info('Message sent successfully to token: ' . $token);
        } catch (NotFound $e) {
            Log::info('Token not found: ' . $e->getMessage());
            $fcmUserToken = FCMRegistrationTokens::where('frt_user_id', $userId)
                ->where('frt_registration_token', $token)->get();

            // if by accident saved the same token more than one time
            foreach ($fcmUserToken as $token) {
                Log::info('delete token: ' . $token);

                $token->delete();
            }
        } catch (InvalidArgument $e) {
            Log::info('Invalid argument: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::info('Error sending message: ' . $e->getMessage());
        }
    }
}
