<?php

namespace App\Notifications;

use App\Models\FcmRegistrationTokensModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;


class TaskNotification extends Notification
{
    use Queueable;

    protected $task;

    /**
     * Create a new notification instance.
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    // Define the notification's delivery channels (Mail, Database, Firebase, etc.)
    public function via($notifiable)
    {
        Log::info('Notification via method called');
        return ['firebase']; // Can be mail, database, firebase, etc.
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toFirebase(object $notifiable)
    {
        Log::info('toFirebase method called');
        Log::info('FCM Tokens:');
        Log::info('FCM Tokens sss');


        // // Retrieve all FCM registration tokens for the user
        // $tokens = FcmRegistrationTokensModel::where('frt_user_id', $notifiable->id) // Match tokens for the user
        //     ->pluck('frt_registration_token') // Get all registration tokens
        //     ->toArray();

        // Log::info('FCM Tokens:', $tokens);


        // if (!empty($tokens)) {

        //     $messaging = app('firebase.messaging');

        //     // Create Firebase Cloud Message with title and body
        //     $message = CloudMessage::new()
        //         ->withNotification(FirebaseNotification::create(
        //             'New Task Assigned', // Title of the notification
        //             'You have been assigned a new task: ' . $this->task->t_content // Body of the notification
        //         ));

        //     // Loop through tokens and send message
        //     foreach ($tokens as $token) {
        //         $messageWithToken = $message->withChangedTarget('token', $token);
        //         $messaging->send($messageWithToken);
        //     }
        // }









        // $messaging = app('firebase.messaging');

        // // Create Firebase Cloud Message
        // $message = CloudMessage::withTarget('token', $notifiable->fcm_token)
        //     ->withNotification(FirebaseNotification::create(
        //         'New Task Assigned',
        //         'You have been assigned a new task: ' . $this->task->t_content
        //     ));

        // // Send the message
        // $messaging->send($message);




        //     return CloudMessage::withTarget('token', $notifiable->fcm_token) // Assuming user has an `fcm_token` field
        //         ->withNotification([
        //             'title' => 'New Task Assigned',
        //             'body' => 'You have been assigned a new task: ' . $this->task->title,
        //         ]);





        //     $messaging = app(Messaging::class);

        //     $message = CloudMessage::withTarget($notifiable->fcm_token) // fcm_token should be user fcm token
        //         ->withNotification(['title' => 'New Task Assigned', 'body' => 'You have been assigned a new task'])
        //         ->withData([
        //             'task_id' => $this->task->id,
        //         ]);

        //     $messaging->send($message);

        // return $message;

        //         // return (new MailMessage)
        //         //     ->line('The introduction to the notification.')
        //         //     ->action('Notification Action', url('/'))
        //         //     ->line('Thank you for using our application!');
    }


    // For Firebase Notification
    // public function toFirebase($notifiable)
    // {
    //     return FirebaseMessage::withTarget('token', $notifiable->fcm_token) // Assuming user has an `fcm_token` field
    //         ->withNotification([
    //             'title' => 'New Task Assigned',
    //             'body' => 'You have been assigned a new task: ' . $this->task->title,
    //         ]);
    // }

    // Store in the database
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'notification toDatabase',
            'task_id' => $this->task->t_id,
            // 'task_title' => $this->task->title,
            // 'description' => $this->task->description,
        ];
    }

    // // For email notifications (if needed)
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->line('You have been assigned a new task.')
    //         ->action('View Task', url('/tasks/' . $this->task->id))
    //         ->line('Task: ' . $this->task->title);
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
