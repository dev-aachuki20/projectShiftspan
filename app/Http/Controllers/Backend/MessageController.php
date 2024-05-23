<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\MessageDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Staff\NotificationRequest;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;
use App\Models\Notification as Notifications;
use Illuminate\Support\Facades\Validator;
use Auth;
class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MessageDataTable $dataTable)
    {  
        abort_if(Gate::denies('message_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = Auth::user();
            $staffsNotify = '';
            if($user->is_super_admin){
                $staffsNotify = User::with(['company'])->where('is_active', 1)->whereNotNull('company_id')->whereHas('company', function ($query) {
                        $query->where('is_active', true);
                    })
                    ->orderBy('id', 'desc')
                    ->get();
            }else{
                $staffsNotify = User::with(['company'])->where('is_active',1)->where('company_id', $user->id)->orderBy('id', 'desc')->get();
            }

            $groups = Group::whereHas('users',function($query){
                $query->where('user_id',auth()->user()->id);
            })->orderByDesc(DB::raw('(SELECT MAX(created_at) FROM messages WHERE group_id = groups.id)'))->get();

            // return $dataTable->render('admin.message.index', compact('staffsNotify'));

            return view('admin.message.index', compact('staffsNotify','groups'));

        } catch (\Exception $e) {
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationRequest $request)
    {
        abort_if(Gate::denies('message_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $input = $request->validated();          
            $input['notification_type'] = 'send_message';

            DB::beginTransaction();

            $users = User::with(['company'])->whereIn('uuid', $input['staffs'])->get();

            if($input['section'] == 'help_chat'){

                foreach($users as $staff){
                    $userIds= [];
                    $group = Group::whereHas('users',function($query) use($staff){
                        $query->where('user_id',$staff->id);
                    })->where('group_name',$input['subject'])->first();

                    if(!$group){
                        $groupDetail['group_name'] = $input['subject'];
                        $groupCreated = Group::create($groupDetail);
                        if($groupCreated){
                            $group = $groupCreated;
                            $userIds[] = $staff->id;
                            $userIds[] = $staff->company->id;
                            $userIds[] = auth()->user()->is_super_admin ? auth()->user()->id : $staff->company->created_by;
                            
                            $groupCreated->users()->attach($userIds);
                        }
                    }
                    
                    $input['group_uuid'] = $group->uuid;

                    //Start to create message
                    $messageInput['group_id'] = $group->id;
                    $messageInput['content']  = $input['message'];
                    $messageInput['type']     = 'text';
                    $messageCreated = Message::create($messageInput);
                    //End to create message

                    if(auth()->user()->company){
                        //Send notification to super admin
                        Notification::send($staff->company->createdBy, new SendNotification($input));
                    }
                    
                     Notification::send($staff, new SendNotification($input));
                   
                }
                
            }else{
                
                Notification::send($users, new SendNotification($input));
            
                
            }

          
           

            if($input['section'] != 'help_chat'){

                //If User is login as super admin
                if(auth()->user()->is_super_admin){
                    $companies = User::whereIn('uuid', $input['companies'])->get();
                    Notification::send($companies, new SendNotification($input));
                }

                //If User is login as company or sub admin
                if(auth()->user()->is_sub_admin){ 
                    $superAdmin = User::whereHas('roles',function($query){
                        $query->where('id',config('constant.roles.super_admin'));
                    })->first();
                    Notification::send($superAdmin, new SendNotification($input));
                }

            }

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.crud.message_sent'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json([
                'success' => false, 
                'error_type' => 'something_error', 
                'error' => trans('messages.error_message'),
                'error_details'=>$e->getMessage().' '.$e->getFile().' '.$e->getLine(),
            ], 400 );
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function massDestroy(NotificationRequest $request)
    {
        abort_if(Gate::denies('message_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            if ($request->ajax()) {
                $input = $request->validated();
                DB::beginTransaction();
                $users = Notifications::whereIn('id', $input['ids'])->delete();
                
                DB::commit();
                if($users){
                    return response()->json(['success' => true, 'message' => trans('messages.crud.delete_record'), 200]);
                }
            }
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }


    public function getGroupList(Request $request){

        if($request->ajax()) {
            try {
                $groups = Group::whereHas('users',function($query){
                    $query->where('user_id',auth()->user()->id);
                });

                if($request->search){
                    $groups = $groups->where('group_name','like','%'.$request->search.'%');
                }
                
                $groups = $groups->orderByDesc(DB::raw('(SELECT MAX(created_at) FROM messages WHERE group_id = groups.id)'))->get();
               
                $viewHTML = view('admin.message.partials.group', compact('groups'))->render();
                
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } catch (\Exception $e) {
                // dd($e);
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );

    }

    public function showChatScreen(Request $request){
        if($request->ajax()) {
            
            try {

                $group = Group::whereHas('users',function($query){
                    $query->where('user_id',auth()->user()->id);
                })->where('uuid',$request->groupId)->first();
               
                if($group){
                    $allMessages = $group->messages()->orderBy('created_at','asc')->get()->groupBy(function($message) {
                        return $message->created_at->format('d-F-Y');
                    });   
                    // $allMessages = $group->messages()->orderBy('created_at','asc')->get();   

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

                    $viewHTML = view('admin.message.partials.chatbox', compact('group','allMessages'))->render();
                }else{
                    $viewHTML = view('admin.message.partials.chatbox')->render();
                }

                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } catch (\Exception $e) {
                // dd($e);
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
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

            $message =  $messageCreated;
            $viewMessageHtml = view('admin.message.partials.message-view', compact('message'))->render();

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.crud.message_sent'),
                'viewMessageHtml'=>$viewMessageHtml
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json([
                'success' => false, 
                'error_type' => 'something_error', 
                'error' => trans('messages.error_message'),
                'error_details'=>$e->getMessage().' '.$e->getFile().' '.$e->getLine(),
            ], 400 );
        }
    }
}
