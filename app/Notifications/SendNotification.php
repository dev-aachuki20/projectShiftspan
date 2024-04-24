<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\CustomNotificationMail;
use Auth;

class SendNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if($notifiable->is_super_admin || $notifiable->is_sub_admin){
            return ['mail', 'database'];
        }else{
            return ['database'];
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');

        $subject = $this->data['subject'];
        $userName = $notifiable->name;
        $message = $this->data['message'];

        return (new CustomNotificationMail($subject, $userName, $message))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => $this->data['subject'],
            'message' => $this->data['message'],
            'section' => $this->data['section'],
        ];
    }
}
