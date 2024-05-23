<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpSendNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    /**
 * Get the mail representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \Illuminate\Notifications\Messages\MailMessage
 */
    public $user,$token, $subject , $expiretime;

    public function __construct($user,$token, $subject , $expiretime)
    {
        $this->user = $user;
        $this->token = $token;
        $this->subject = $subject;
        $this->expiretime = $expiretime;
    }


    public function build()
    {
        return $this->view('emails.auth.forgot_password_otp',['user' => $this->user ,'token' => $this->token ,'expiretime' => $this->expiretime]);
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        //dd($notifiable->email);
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.auth.forgot_password_otp', ['user' => $this->user ,'token' => $this->token ,'expiretime' => $this->expiretime]);
           // ->to($notifiable->email);
    }

}
