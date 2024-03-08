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
        
        /* Send the immidate notification */
        $user_id = $notifiable->id;
        if(auth()->user()->is_super_admin){
            sendNotification($user_id, $data['subject'], $data['message'], $data['section'], $data['notification_type'], $data);
        }elseif(auth()->user()->is_sub_admin){
            sendNotification($user_id, $data['subject'], $data['message'], $data['section'], $data['notification_type'], $data);
        }

        /* From this Save the value from database */
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
