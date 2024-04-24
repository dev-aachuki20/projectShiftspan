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
            return $dataTable->render('admin.message.index', compact('staffsNotify'));
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

            $users = User::whereIn('uuid', $input['staffs'])->get();
            Notification::send($users, new SendNotification($input));

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

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.crud.message_sent'),
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
}
