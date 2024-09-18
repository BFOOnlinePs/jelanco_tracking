<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

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
    public function via($notifiable): array
    {
        return ['fcm', 'database', 'firebase']; // Can be mail, database, firebase, etc.
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toFcm(object $notifiable)
    {
        return CloudMessage::withTarget('token', $notifiable->fcm_token) // Assuming user has an `fcm_token` field
            ->withNotification([
                'title' => 'New Task Assigned',
                'body' => 'You have been assigned a new task: ' . $this->task->title,
            ]);

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

    // // Store in the database
    // public function toDatabase($notifiable)
    // {
    //     return [
    //         'task_id' => $this->task->id,
    //         'task_title' => $this->task->title,
    //         'description' => $this->task->description,
    //     ];
    // }

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
