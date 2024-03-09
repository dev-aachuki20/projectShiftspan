<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\APIController;
use App\Http\Requests\Api\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Notifications\SendNotification;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NotificationController extends APIController
{
    public function getAnnouncements(NotificationRequest $request){
        try {
            $input = $request->validated();
            $user = auth()->user();
            $notifications = $user->notifications()->where('notifiable_id', $user->id)->whereSection('announcements')->whereNull('deleted_at')->get();

            return $this->respondOk([
                'status'        => true,
                'message'       => trans('messages.record_retrieved_successfully'),
                'data'          => NotificationResource::collection($notifications),
            ])->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function helpChats(Request $request){
        try {
            $user = auth()->user();
            
            $notification = $user->notifications()->where('notifiable_id', $user->id)->whereSection('help_chat')->whereNull('deleted_at')->get();
            return $this->respondOk([
                'status'        => true,
                'message'       => trans('messages.record_retrieved_successfully'),
                'data'          => NotificationResource::collection($notification),
            ])->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function helpChatNotification(NotificationRequest $request){
        try {
            $input = $request->validated();
            $user = auth()->user();
            $input['subject'] = 'Help Chat';
            $input['notification_type'] = 'send_message';
            DB::beginTransaction();

            Notification::send($user, new SendNotification($input));
            $notification = $user->notifications()->latest()->first();

            DB::commit();
            return $this->respondOk([
                'status'        => true,
                'message'       => trans('messages.record_retrieved_successfully'),
                'data'          => new NotificationResource($notification),
            ])->setStatusCode(Response::HTTP_OK);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }
}
