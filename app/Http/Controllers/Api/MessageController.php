<?php

namespace App\Http\Controllers\Api;



use App\Models\Group;
use App\Models\Message;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Api\APIController;
use Symfony\Component\HttpFoundation\Response;


class MessageController extends APIController
{
    public function getGroupSubjects(){
        try {
            $data['subjects'] =   getSetting('message_subject');
           
            return $this->respondOk([
                'status'        => true,
                'message'       => trans('messages.record_retrieved_successfully'),
                'data'          => $data,
            ])->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function storeGroupMessage(Request $request){

        $allSubjects = implode(',',getSetting('message_subject'));
        $input = $request->validate([
            'subject'   => ['required','string','in:'.$allSubjects],
            'message'   => ['required','string'],
        ]); 

        try {
            DB::beginTransaction();

            $input['notification_type'] = 'send_message';
            $input['section'] = 'help_chat';

            $authUser = auth()->user();

            $group = Group::whereHas('users',function($query) use($authUser){
                $query->where('user_id',$authUser->id);
            })->where('group_name',$input['subject'])->first();

            if(!$group){
                $groupDetail['group_name'] = $input['subject'];
                $groupCreated = Group::create($groupDetail);
                if($groupCreated){
                    $group = $groupCreated;
                    $userIds[] = $authUser->company_id;
                    $userIds[] = $groupCreated->created_by;

                    $groupCreated->users()->attach($userIds);
                }
            }

            //Start to create message
            $messageInput['group_id'] = $group->id;
            $messageInput['content']  = $input['message'];
            $messageInput['type']     = 'text';
            $messageCreated = Message::create($messageInput);
            //End to create message
                
            Notification::send($authUser->company, new SendNotification($input));

            DB::commit();
            return $this->respondOk([
                'status'        => true,
                'message'       => trans('messages.crud.message_sent'),
                'data'          => [],
            ])->setStatusCode(Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }

    }

    public function getGroupList(){
        try {
            $data['groups'] = Group::whereHas('users',function($query){
                $query->where('user_id',auth()->user()->id);
            })->get();
           
            return $this->respondOk([
                'status'        => true,
                'message'       => trans('messages.record_retrieved_successfully'),
                'data'          => $data,
            ])->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

}
