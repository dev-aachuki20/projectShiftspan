<?php
namespace App\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;
use Illuminate\Notifications\Notification;
use Auth;

class DatabaseChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->data;
    
        return $notifiable->routeNotificationFor('database')->create([
            'id'                => $notification->id,
            'type'              => get_class($notification),
            'data'              => ['data' => $data],
            'section'           => $data['section'],
            'subject'           => $data['subject'],
            'message'           => $data['message'],
            'notification_type' => $data['notification_type'],
            'read_at'           => null,
            'created_by'        => Auth::user()->id,
        ]);
    }

}
