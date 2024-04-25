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
            $notifications = $user->notifications()->where('notifiable_id', $user->id)->whereSection($input['section'])->whereNull('deleted_at')->get();

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

    public function helpChats(NotificationRequest $request){
        try {
            $input = $request->validated();
            $user = auth()->user();
            
            $notification = $user->notifications()->where('notifiable_id', $user->id)->whereSection($input['section'])->whereNull('deleted_at')->get();
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

            Notification::send($user->company, new SendNotification($input));
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

    public function makeAsRead($uuid){

        DB::beginTransaction();
        try {
            $user = auth()->user();
        
            $notification = $user->notifications()->where('id',$uuid)->update(['read_at'=>now()]);
            
            if (!$notification) {
                return $this->respondOk([
                    'status'   => true,
                    'message'   => trans('messages.notification.not_found')
                ])->setStatusCode(Response::HTTP_OK);
            }

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.notification.mark_as_read')
            ])->setStatusCode(Response::HTTP_OK);
            
        } catch (\Exception $e) {
            DB::rollBack();
            // return $this->throwValidation([$e->getMessage()]);
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }
}
