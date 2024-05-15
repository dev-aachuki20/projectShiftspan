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

            $authUser = auth()->user();

            $group = Group::whereHas('users',function($query) use($authUser){
                $query->where('user_id',$authUser->id);
            })->where('group_name',$input['subject'])->first();

            if(!$group){
                $groupDetail['group_name'] = $input['subject'];
                $groupCreated = Group::create($groupDetail);
                if($groupCreated){
                    $group = $groupCreated;
                    $userIds[] = $groupCreated->created_by;
                    $userIds[] = $authUser->company_id;
                    $userIds[] = $authUser->company->created_by;
                    $groupCreated->users()->attach($userIds);
                }
            }

            //Start to create message
            $messageInput['group_id'] = $group->id;
            $messageInput['content']  = $input['message'];
            $messageInput['type']     = 'text';
            $messageCreated = Message::create($messageInput);
            //End to create message

            $input['group_uuid'] = $group->uuid;
            $input['notification_type'] = 'send_message';
            $input['section'] = 'help_chat';
            
            if($authUser->company){
                Notification::send($authUser->company, new SendNotification($input));
            }
            
            if($authUser->company->createdBy){
                Notification::send($authUser->company->createdBy, new SendNotification($input));
            }
            

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
             $groups = Group::whereHas('users',function($query){
                $query->where('user_id',auth()->user()->id);
            })->orderByDesc(DB::raw('(SELECT MAX(created_at) FROM messages WHERE group_id = groups.id)'))->get();
           
            foreach($groups as $group){
                

                $group->total_unread_message =  $group->messages()->where('group_id',$group->id)->where('user_id','!=',auth()->user()->id)->whereDoesntHave('usersSeen', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                })->count();
                $group->latestMessages = $group->messages()->orderBy('created_at','desc')->first();   

                $group->latestMessages->user_name = ($group->latestMessages->user->id == auth()->user()->id) ? 'You' : explode(' ',$group->latestMessages->user->name)[0];
               
            }

            $data['groups'] = $groups;
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

    public function getAllMessages($groupId){
        try {
           
            $group = Group::whereHas('users',function($query){
                $query->where('user_id',auth()->user()->id);
            })->where('uuid',$groupId)->first();
           
            $data = [];
            if($group->messages){
                $allMessages = $group->messages()->with('user')->orderBy('created_at','asc')->get();   

                $user = auth()->user();
    
                // $unseenMessage = $user->messages()->where('group_id',$group->id)->where('user_id','!=',$user->id)->whereDoesntHave('usersSeen', function ($query) {
                //     $query->where('user_id', auth()->user()->id);
                // })->count();
    
                $messageIds = $group->messages()->where('user_id','!=',$user->id)->whereDoesntHave('usersSeen', function ($query) {
                         $query->where('user_id', auth()->user()->id);
                    })->pluck('id')->toArray();
    
                $exists = $user->seenMessage()->wherePivotIn('message_id', $messageIds)->exists();
    
                if (!$exists) {
                    $user->seenMessage()->attach($messageIds, ['group_id' => $group->id,'read_at'=>now()]);
                }
    
                $data['groups'] = $group;
                $data['messages'] = $allMessages;
    
            }
            
           return $this->respondOk([
               'status'        => true,
               'message'       => trans('messages.record_retrieved_successfully'),
               'data'          => $data,
           ])->setStatusCode(Response::HTTP_OK);
       } catch (\Exception $e) {

            // dd($e->getMessage().' '.$e->getFile().' '.$e->getCode());

           \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
           return $this->throwValidation([trans('messages.error_message')]);
       }
    }

   public function sendMessage(Request $request,$groupId){
        $input = $request->validate([
            'message_content'=>['required']
        ]); 

        try {
            DB::beginTransaction();

            $user = auth()->user();

            $group = Group::whereHas('users',function($query) use($user){
                $query->where('user_id',$user->id);
            })->where('uuid',$groupId)->first();

            //Start to create message
            $messageInput['group_id'] = $group->id;
            $messageInput['content']  = $input['message_content'];
            $messageInput['type']     = 'text';
            $messageCreated = Message::create($messageInput);
            //End to create message
              
            $groupUsersExpectAuthUser = $group->users()->where('id','!=',$user->id)->get();
            
            if($groupUsersExpectAuthUser->count() > 0){
                //Send notfication to staff
                $notificationInput['group_uuid'] = $group->uuid;
                $notificationInput['subject'] = $group->group_name;
                $notificationInput['message'] = $input['message_content'];
                $notificationInput['notification_type'] = 'send_message';
                $notificationInput['section'] = 'help_chat';

                Notification::send($groupUsersExpectAuthUser, new SendNotification($notificationInput));
            }

            DB::commit();
                
            $data['message'] = $messageCreated;

            return $this->respondOk([
                'status'        => true,
                'message'       => trans('messages.crud.message_sent'),
                'data'          => $data,
            ])->setStatusCode(Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
                // dd($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
   }


}
