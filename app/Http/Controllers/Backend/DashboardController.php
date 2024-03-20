<?php

namespace App\Http\Controllers\Backend;

use App\Models\Location;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [
            'userCount' => 0, 'shiftCount' => 0, 'locationCount' => 0
        ];
        if($user->roles->first()->name == 'Super Admin'){
            $data['userCount'] = User::whereNotNull('company_id')->count();
            $data['shiftCount'] = Shift::Count();
            $data['locationCount'] = Location::count();
        }else/* if($user->roles->first()->name == 'Sub Admin') */{
            $data['userCount'] = User::where('company_id', $user->id)->count();
            $data['shiftCount'] = Shift::where('sub_admin_id', $user->id)->count();
            $data['locationCount'] = $user->locations()->count();
        }

        return view('admin.dashboard', compact('data'));
    }

    public function notification(Request $request){
        try {
            if($request->ajax()) {
                $user = Auth::user();
                $notification = '';
                if($user->is_super_admin){
                    $notification = Notification::select('id', 'notifiable_id', 'subject', 'message', 'section', 'notification_type')
                        ->whereNull('read_at')->where('id','!=', config('constant.roles.super_admin'))->orderBy('created_at', 'desc')->get();
                }else{
                    $notification = Notification::select('id', 'notifiable_id', 'subject', 'message', 'section', 'notification_type')
                        ->whereNull('read_at')->whereNotIN('created_by', [
                            config('constant.roles.super_admin'), config('constant.roles.sub_admin')
                        ])->orderBy('created_at', 'desc')->get()
                    ->filter(function($item) use ($user) {
                        return $user->id == $item->user;
                    })->sortByDesc('created_at');
                }               
                
                $viewHTML = view('partials.notification', compact('notification'))->render(); 
                return response()->json(['success' => true, 'htmlView'=>$viewHTML]);
            } 
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }  catch (\Exception $e) {
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine().' '.$e->getCode());          
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    public function readNotification(Request $request)
    {
        DB::beginTransaction();
        try {
            if($request->ajax()){
                $notification = Notification::where('id', $request->notification)->first();
                if ($notification) {
                    $notification->update(['read_at' => now()]);
                    DB::commit();
                    return response()->json(['success' => true]);
                }
            }
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine().' '.$e->getCode());          
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }
}
