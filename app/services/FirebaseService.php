<?php
namespace App\Services;

use Kreait\Firebase\Contract\Messaging as ContractMessaging;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseService
{
    protected $messaging;

    public function __construct(ContractMessaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendNotification($title, $body, $token)
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ]);

        $this->messaging->send($message);
    }
}
