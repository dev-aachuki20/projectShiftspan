<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $messageSentBy = in_array($this->created_by, [
            config('constant.roles.super_admin'), config('constant.roles.sub_admin')
            ]) ? $this->created_by : null;
            
        return [
            'id'                => $this->id,
            // 'user_id'           => $this->notifiable_id,
            'subject'           => $this->subject,
            'message'           => $this->message,
            'section'           => $this->section,
            'notification_type' => $this->notification_type,
            'message_sent_by'   => $messageSentBy,
            'read_at'           => $this->read_at,
            'created_at'        => $this->created_at,
        ];
    }
}
