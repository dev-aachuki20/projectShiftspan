<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\ShiftDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shift\StoreRequest;
use App\Http\Requests\Shift\UpdateRequest;
use App\Models\AuthorizedShift;
use App\Models\ClientDetail;
use App\Models\ClockInOut;
use App\Models\Occupation;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;
use Carbon\Carbon;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ShiftDataTable $dataTable)
    {
        abort_if(Gate::denies('shift_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try{
            return $dataTable->render('admin.shift.index');
        }
        catch(\Exception $e){
            abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('shift_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()){
            try{
                if(auth()->user()->is_super_admin){
                    $subAdmins = User::whereHas('roles', function($q){ $q->where('id', config('constant.roles.sub_admin')); })->pluck('name', 'uuid');
                    
                    $viewHTML = view('admin.shift.create', compact('subAdmins'))->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
                }

                // for sub admin login
                $compactData = $this->getShiftViewData(auth()->user()->uuid);
                $viewHTML = view('admin.shift.create', $compactData)->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } catch(\Exception $e){
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }  


    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('shift_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) 
        {
            DB::beginTransaction();
            try{
                $input = $this->modifyRequestInput($request);

                foreach ($request->shifts as $shiftData)
                {                                       
                    $data = [
                        'shift_label' => $input['shift_label'],
                        'sub_admin_id' => $input['sub_admin_id'],
                        'client_detail_id' => $input['client_detail_id'],
                        'occupation_id' =>  $input['occupation_id'] ,
                        'start_date' => Carbon::createFromFormat('d-m-Y', $shiftData['start_date'])->format('Y-m-d'),
                        'end_date' => Carbon::createFromFormat('d-m-Y', $shiftData['end_date'])->format('Y-m-d'),
                        'start_time' => $shiftData['start_time'],
                        'end_time' => $shiftData['end_time'],
                    ];       
                    
                    $clientDetail = ClientDetail::where('id', $input['client_detail_id'])->first();
                    if($request->quantity){
                        for($i=1;$i<=$request->quantity; $i++){
                            $shift = Shift::create($data);
                            if($i == 1 && $shift && isset($shiftData['assign_staff']) && !empty($shiftData['assign_staff'])){
                                $shift->update([
                                    'picked_at' => date('Y-m-d H:i:s'),
                                    'status' => 'picked',
                                ]);                            
                                $staffId = User::where('uuid', $shiftData['assign_staff'])->first()->id;
                                $shift->staffs()->sync([$staffId => ['created_at' => date('Y-m-d H:i:s')]]);
                            }
                        }
                    }
                    
                    if(isset($shiftData['assign_staff']) && !is_null($shiftData['assign_staff']) && !empty($shiftData['assign_staff'])){
                        $user = User::where('uuid', $shiftData['assign_staff'])->first();
                       
                        $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
                        $messageData = [
                            'notification_type' => array_search(config('constant.subject_notification_type.shift_assign'), config('constant.subject_notification_type')),
                            'section'           => $key,
                            'subject'           => trans('messages.shift.shift_created_and_assign_subject'),
                            'message'           => trans('messages.shift.shift_created_and_assign_message', [
                                'username'      => $user->name,
                                'start_date'    => $shiftData['start_date'], 
                                'end_date'      => $shiftData['end_date'], 
                                'start_time'    => $shiftData['start_time'], 
                                'end_time'      => $shiftData['end_time']
                            ]),       
                        ];                        
                        Notification::send($user, new SendNotification($messageData));
                    }
                    if(isset($input['sub_admin_id']) && !empty($request->quantity) && $request->quantity > 1){                     
                        $companyAdmin = User::where('id', $input['sub_admin_id'])->first();
                        $staffs = $companyAdmin->staffs()->where('uuid', "<>", $shiftData['assign_staff'])->get();
                        $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
                        $messageData = [
                            'notification_type' => array_search(config('constant.subject_notification_type.shift_available'), config('constant.subject_notification_type')),
                            'section'           => $key,
                            'subject'           => trans('messages.shift.shift_available_subject'),
                            'message'           => trans('messages.shift.shift_available_staff_message', [
                                'shift_label'   => $request['shift_label'],
                                'start_date'    => $shiftData['start_date'], 
                                'listed_business' => $clientDetail->name, 
                                'start_time'    => $shiftData['start_time'], 
                                'end_time'      => $shiftData['end_time']
                            ]),       
                        ];
                        Notification::send($staffs, new SendNotification($messageData));  
                    }

                    $shiftCreatorRole = $shift->shiftCreator->roles()->first()->id;
                    if(config('constant.roles.super_admin') == $shiftCreatorRole){
                        $adminData = $shift->client;
                    } else if(config('constant.roles.sub_admin') == $shiftCreatorRole) {
                        $adminData = User::whereHas('roles', function($q){
                            $q->where('id', config('constant.roles.super_admin'));
                        })->first();
                    }

                    // send notification to admin
                    $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
                    $adminMessageData = [
                        'notification_type' => array_search(config('constant.subject_notification_type.shift_available'), config('constant.subject_notification_type')),
                        'section'           => $key,
                        'subject'           => trans('messages.shift.shift_available_subject'),
                        'message'           => trans('messages.shift.shift_available_admin_message', [
                            'shift_label'   => $request['shift_label'],
                            'start_date'    => $shiftData['start_date'], 
                            'listed_business' => $clientDetail->name, 
                            'start_time'    => $shiftData['start_time'], 
                            'end_time'      => $shiftData['end_time']
                        ]),       
                    ];
                    Notification::send($adminData, new SendNotification($adminMessageData));  
                }   
                DB::commit();
                $response = [
                    'success' => true,
                    'message' => trans('cruds.shift.title_singular').' '.trans('messages.crud.add_record'),
                ];
                return response()->json($response);
            } catch (\Throwable $th) {
                DB::rollBack();
                //  dd($th);
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
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
    public function edit(Request $request, string $id)
    {
        abort_if(Gate::denies('shift_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                $shift = Shift::with(['client', 'clientDetail', 'occupation', 'staffs'])->where('uuid', $id)->first();
                $selectedStaffs = $shift->staffs()->pluck('uuid')->toArray();
                if(auth()->user()->is_super_admin){
                    $subAdmins = User::whereHas('roles', function($q){ $q->where('id', config('constant.roles.sub_admin')); })->pluck('name', 'uuid');
                    $compactData = array_merge(compact('subAdmins', 'shift', 'selectedStaffs'), $this->getShiftViewData($shift->client->uuid));

                    $viewHTML = view('admin.shift.edit', $compactData)->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
                }

                // for sub admin login
                $compactData = array_merge(compact('shift', 'selectedStaffs'), $this->getShiftViewData(auth()->user()->uuid));
                $viewHTML = view('admin.shift.edit', $compactData)->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } 
            catch (\Exception $e) {
                // dd($e);
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        abort_if(Gate::denies('shift_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) 
        {
            DB::beginTransaction();
            try{
                $shift = Shift::where('uuid', $id)->first();
                $input = $this->modifyRequestInput($request);
                
                $shift->update($input);

                if($request->has('assign_staff') && !empty($request->assign_staff)){
                    $shift->update([
                        'picked_at' => date('Y-m-d H:i:s'),
                        'status' => 'picked',
                    ]);                            
                    $staffId = User::where('uuid', $request->assign_staff)->first();
                    $shift->staffs()->sync([$staffId->id => ['created_at' => date('Y-m-d H:i:s')]]);

                    /* Send Notification */
                    /*if($request->assign_staff){
                        $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
                        $messageData = [
                            'notification_type' => array_search(config('constant.subject_notification_type.shift_changes'), config('constant.subject_notification_type')),
                            'section'           => $key,
                            'subject'           => trans('messages.shift.shift_picked_update_subject'),
                            'message'           => trans('messages.shift.shift_picked_update_message', [
                                'username'      => $staffId->name,
                                // 'admin'         => getSetting('site_title') ? getSetting('site_title') : config('app.name'),
                            ]),
                        ];
                        
                        Notification::send($shift->staffs->first(), new SendNotification($messageData));
                    }*/
                    
                }else{
                    $shift->update([
                        'picked_at' => null,
                        'status' => 'open',
                    ]);                            
                    $shift->staffs()->sync([]);
                }
                DB::commit();

                $response = [
                    'success' => true,
                    'message' => trans('cruds.shift.title_singular').' '.trans('messages.crud.update_record'),
                ];
                return response()->json($response);
            } catch (\Exception $e) {
                DB::rollBack();                
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('shift_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $shift = Shift::with('staffs')->where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                if ($shift->staffs) {
                    $shift->staffs()->sync([]);
                }
                $shift->delete();
                if(!empty($shift->staffs->first())){
                    $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
                    $messageData = [
                        'notification_type' => array_search(config('constant.subject_notification_type.shift_delete'), config('constant.subject_notification_type')),
                        'section'           => $key,
                        'subject'           => trans('messages.shift.shift_delete_subject'),
                        'message'           => trans('messages.shift.shift_delete_message', [
                            'username'      => $shift->staffs->first()->name,
                            // 'admin'         => getSetting('site_title') ? getSetting('site_title') : config('app.name'),
                        ]),
                    ];
                    
                    Notification::send($shift->staffs->first(), new SendNotification($messageData));
                }
                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('messages.crud.delete_record'),
                ];
                return response()->json($response);
            } catch (\Exception $e) {
                DB::rollBack();                
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('shift_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'ids'   => 'required|array',
                'ids.*' => 'exists:shifts,uuid',
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }else{
                DB::beginTransaction();
                try {
                    $ids = $request->input('ids');
                    $shfits = Shift::with('staffs')->whereIn('uuid', $ids)->get();
                    foreach($shfits as $shift){
                        if ($shift->staffs) {
                            $shift->staffs()->sync([]);
                        }
                        $shift->delete();

                        if(!empty($shift->staffs->first())){
                            $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
                            $messageData = [
                                'notification_type' => array_search(config('constant.subject_notification_type.shift_delete'), config('constant.subject_notification_type')),
                                'section'           => $key,
                                'subject'           => trans('messages.shift.shift_delete_subject'),
                                'message'           => trans('messages.shift.shift_delete_message', [
                                    'username'      => $shift->staffs->first()->name,
                                    // 'admin'         => getSetting('site_title') ? getSetting('site_title') : config('app.name'),
                                ]),
                            ];
                            
                            Notification::send($shift->staffs->first(), new SendNotification($messageData));
                        }
                    }
                    
                    DB::commit();
                    $response = [
                        'success'    => true,
                        'message'    => trans('messages.crud.delete_record'),
                    ];
                    return response()->json($response);
                } catch (\Exception $e) {
                    DB::rollBack();                
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
                }
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    public function getSubAdminData (Request $request){
        if($request->ajax()){
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,uuid',
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }else{
                try {
                    $subData = $this->getShiftViewData($request->id);

                    // staffs options
                    $staffsOptionHtml = '<option value="">'.(trans('global.select').' '.trans('cruds.staff.title_singular')).'</option>';
                    foreach($subData['staffs'] as $staffKey => $staff){
                        $staffsOptionHtml .= '<option value="'.$staffKey.'" >'.$staff.'</option>';
                    }

                    // occupations options
                    $occupationsOptionHtml = '<option value="">'.(trans('global.select').' '.trans('cruds.occupation.title_singular')).'</option>';
                    foreach($subData['occupations'] as $occupationKey => $occupation){
                        $occupationsOptionHtml .= '<option value="'.$occupationKey.'" >'.$occupation.'</option>';
                    }

                    // clientDetails options
                    $clientDetailsOptionHtml = '<option value="">'.(trans('global.select').' '.trans('cruds.shift.fields.client_detail_name')).'</option>';
                    foreach($subData['clientDetails'] as $clientDetailKey => $clientDetail){
                        $clientDetailsOptionHtml .= '<option value="'.$clientDetailKey.'" >'.$clientDetail.'</option>';
                    }

                    $response = [
                        'success'    => true,
                        'staff_html' => $staffsOptionHtml,
                        'occupation_html' => $occupationsOptionHtml,
                        'client_detail_html' => $clientDetailsOptionHtml,
                    ];
                    return response()->json($response);
                } catch (\Exception $e) {
                    // dd($e);
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
                }
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    private function getShiftViewData($userId){
        $user = User::whereUuid($userId)->first();

        $occupations = $user->occupations()->pluck('name', 'uuid');
        $staffs = $user->staffs()->pluck('name', 'uuid');
        $clientDetails = $user->clientDetails()->pluck('name', 'uuid');

        return [
            'staffs' => $staffs,
            'occupations' => $occupations,
            'clientDetails' => $clientDetails,
        ];
    }

    private function modifyRequestInput($req){
        $input = $req->except('quantity');

        if((auth()->user()->is_super_admin)){
            $input['sub_admin_id'] = User::where('uuid', $req->sub_admin_id)->first()->id;
        } else {
            $input['sub_admin_id'] = auth()->user()->id;
        }
        $input['client_detail_id']  = ClientDetail::where('uuid', $req->client_detail_id)->first()->id;
        $input['occupation_id']     = Occupation::where('uuid', $req->occupation_id)->first()->id;

        $input['start_date']        = date('Y-m-d', strtotime($req->start_date));
        $input['end_date']          = date('Y-m-d', strtotime($req->end_date));
        $input['start_time']        = date('H:i:s', strtotime($req->start_time));
        $input['end_time']          = date('H:i:s', strtotime($req->end_time));

        if($req->start_date != $req->end_date){
            $startTime = Carbon::parse($req->start_time);
            $endTime = Carbon::parse($req->end_time);
            
            if ($startTime->gt($endTime)) {
                $input['shift_type'] = 1;
            } else {
                $input['shift_type'] = 0;
            }
        }

        return $input;
    }

    public function CancelShift(Request $request, $id){
        // abort_if(Gate::denies('shift_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $shift = Shift::where('uuid', $id)->first();
            DB::beginTransaction();
            try {       
                
                $shift->update(['status' => 'cancel', 'cancel_at' => date('Y-m-d H:i:s')]);

                $shift = Shift::where('uuid', $id)->first();

                if($shift->staffs->first()){
                    $user = $shift->staffs->first();
                    $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
                    $messageData = [
                        'notification_type' => array_search(config('constant.subject_notification_type.shift_cancels'), config('constant.subject_notification_type')),
                        'section'           => $key,
                        'subject'           => trans('messages.shift.shift_canceled_subject'),
                        'message'           => trans('messages.shift.shift_canceled_staff_message', [
                            'username'      => $user->name,
                            'shift_label'   => $shift->shift_label,
                            'listed_business' => $shift->clientDetail->name,
                            'cancelled_date'    => Carbon::parse($shift->cancel_at)->format('l d-m-Y'),
                        ]),
                    ];
                    Notification::send($user, new SendNotification($messageData));
                }
              

                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('messages.shift_cancelled'),
                ];
                return response()->json($response);
            } catch (\Exception $e) {
                DB::rollBack(); 
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    public function RateShift(Request $request, $id){
        // abort_if(Gate::denies('shift_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'rating' => ['required', 'in:'.implode(',', array_keys(config('constant.ratings')))]
        ]);
        if ($request->ajax()) {
            $shift = Shift::where('uuid', $id)->first();
            DB::beginTransaction();
            try {                
                $shift->update(['rating' => $request->rating]);

                /* Send Notification */
                $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
                $messageData = [
                    'notification_type' => array_search(config('constant.subject_notification_type.shift_ratings'), config('constant.subject_notification_type')),
                    'section'           => $key,
                    'subject'           => trans('messages.shift.shift_rating_subject'),
                    'message'           => trans('messages.shift.shift_rating_message', [
                        'username'      => $shift->staffs->first()->name,
                        'rating'        => $request->rating,
                        'listed_business' => $shift->clientDetail->name,
                    ]),
                ];
                
                Notification::send($shift->staffs->first(), new SendNotification($messageData));
                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('messages.rating_shift'),
                ];
                return response()->json($response);
            } catch (\Exception $e) {
                DB::rollBack();          
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    public function clockInAndClockOut(Request $request)
    {
        abort_if(Gate::denies('shift_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            if($request->ajax()){
                $shiftData = '';
                $type = '';
                if($request->type === 'ClockIn' || $request->type === 'ClockOut'){
                    $shiftData = ClockInOut::select(
                        'clockin_date',
                        'clockout_date', 
                        'clockin_latitude', 
                        'clockin_longitude', 
                        'clockout_latitude', 
                        'clockout_longitude')->where('shift_id', $request->shift_id)
                        ->orderBy('id', 'desc')
                        ->get();
                    $type = $request->type;
                } else {
                    $shiftData = AuthorizedShift::where('shift_id', $request->shift_id)->first();
                    $type = $request->type;
                }
                
                $viewHTML = view('admin.shift.clock-in-out', compact('shiftData', 'type'))->render();
                return response()->json([
                    'success' => true, 
                    'htmlView'=>$viewHTML
                ]);
            }
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }catch(\Exception $e){
            // \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }
}
