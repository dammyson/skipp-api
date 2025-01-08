<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChanged extends Notification
{
    use Queueable;

    private $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels. 'mail',  return ['database'];
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [ \App\Channels\FirebaseChannel::class];
       //return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

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

    /**
     * Define the array to be stored in the database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->details['title'],
            'body' => $this->details['body'],
            'url' => $this->details['url'],
            'user_id' => $notifiable->id,
        ];
    }

    /**
     * Get the Firebase representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toFirebase($notifiable)
    {
        return [
            'title' => 'Item Recall Notification',
            'body' => "The item ",
            'data' => [
                'item_name' => "Data",
                'recall_reason' => "Data",
                'action_url' => url('/recalls'),
            ],
        ];
    }
}
