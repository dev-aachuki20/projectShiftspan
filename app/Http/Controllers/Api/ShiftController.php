<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Shift;
use App\Models\ClockInOut;
use Illuminate\Http\Request;
use App\Models\AuthorizedShift;
use Illuminate\Support\Facades\DB;
use App\Rules\NoMultipleSpacesRule;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;


class ShiftController extends APIController
{
    public function availableShifts(Request $request){
        
        $request->validate([
            'location' => ['nullable', 'exists:locations,id,deleted_at,NULL'],
            'occupation' => ['nullable', 'exists:occupations,id,deleted_at,NULL'],
        ]);

        try{
           $user = auth()->user();

           $currentDateTime = Carbon::now();

           $companyShifts = $user->company->companyShifts()->with(['client', 'clientDetail', 'occupation'])
            ->select('id', 'shift_label','sub_admin_id', 'client_detail_id', 'location_id', 'occupation_id', 'start_date', 'start_time', 'end_date', 'end_time')
            ->whereStatus('open')
            ->where(function ($query) use($request) {
                if($request->has('location') && !empty($request->location)){
                    $locationId = $request->location;
                    $query->whereHas('location', function($q) use($locationId){
                        $q->where('id', $locationId);
                    });
                }
                if($request->has('occupation') && !empty($request->occupation)){
                    $occupationId = $request->occupation;
                    $query->whereHas('occupation', function($q) use($occupationId){
                        $q->where('id', $occupationId);
                    });
                }
            })
            ->where(DB::raw("CONCAT(end_date, ' ', end_time)"), '>=', $currentDateTime->toDateTimeString())
            ->orderBy('start_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->get();

            $shiftsData = [];
            // $shiftsData['count'] = $companyShifts->count();
            foreach($companyShifts as $key => $shift){
                $shiftsData[] = [
                    'shift_id'          => $shift->id,
                    'shift_label'       => $shift->shift_label,
                    'sub_admin_name'    => $shift->client->name,
                    'company_address'   => $shift->clientDetail->address,
                    'occupation_name'   => $shift->occupation->name,
                    'location_name'     => $shift->location->name,
                    'start_date'        => $shift->start_date,
                    'end_date'          => $shift->end_date,
                    'start_time'        => $shift->start_time,
                    'end_time'          => $shift->end_time,
                    'build_image'       => $shift->clientDetail->building_image_url ? $shift->clientDetail->building_image_url : asset(config('constant.default.building-image')),
                ];
            }

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.record_retrieved_successfully'),
                'data'      => $shiftsData,
            ])->setStatusCode(Response::HTTP_OK);
        } 
        catch(\Exception $e){
            // dd($e->getMessage().'->'.$e->getLine());
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function completedShifts(Request $request){

        $request->validate([
            'type'=>'required|in:shift,schedule',
        ]);

        try{
            $user = auth()->user();

            $completedShifts = $user->assignShifts()->with(['client', 'clientDetail','occupation','location'])
            ->select('id','shift_label','sub_admin_id', 'client_detail_id','occupation_id','location_id',  'start_date', 'start_time', 'end_date', 'end_time','is_authorized')
            ->whereStatus('complete');

            if($request->type == 'shift'){
                $completedShifts =   $completedShifts->where('is_authorized',1);
            }else if($request->type == 'schedule'){
                $completedShifts =   $completedShifts->where('is_authorized',0);
            }

            $completedShifts =   $completedShifts->orderBy('start_date', 'desc')->get();

            $shiftsData = [];
            $totalWorkingHour = 0;
            $shiftsData['count'] = $completedShifts->count();
            foreach($completedShifts as $shift){
                $startDate = Carbon::parse($shift->start_date);
                $endDate = Carbon::parse($shift->end_date);
                $startTime = Carbon::parse($shift->start_time);
                $endTime = Carbon::parse($shift->end_time);

                $timeDifferenceInMinutes = calculateTimeDifferenceInMinutes($startTime, $endTime);
                $totalWorkingHour += $timeDifferenceInMinutes / 60;

                $shiftsData['records'][] = [
                    'shift_id'          => $shift->id,
                    'shift_label'       => $shift->shift_label,
                    'sub_admin_name'    => $shift->client->name,
                    'company_address'   => $shift->clientDetail->address,
                    'occupation_name'   => $shift->occupation->name,
                    'location_name'     => $shift->location->name,
                    'start_date'        => $shift->start_date,
                    'end_date'          => $shift->end_date,
                    'start_time'        => $shift->start_time,
                    'end_time'          => $shift->end_time,
                ];
            }
            $shiftsData['total_hours'] = $totalWorkingHour;

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.record_retrieved_successfully'),
                'data'      => $shiftsData,
            ])->setStatusCode(Response::HTTP_OK);
        } 
        catch(\Exception $e){
            // dd($e->getMessage().'->'.$e->getLine());
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }
    
    public function upcomingShifts(){
        try{
            $currentDateTime = Carbon::now();
            
            $user = auth()->user();

            $upcomingShifts = $user->assignShifts()->with(['client', 'clientDetail','occupation','location'])
            ->select('id', 'shift_label','sub_admin_id', 'client_detail_id','occupation_id','location_id', 'start_date', 'start_time', 'end_date', 'end_time', 'shift_type')->whereStatus('picked')
            ->orderBy('start_date', 'desc')
            ->get();

            $shiftsData = [];
            $totalWorkingHour = 0;
            $shiftsData['count'] = $upcomingShifts->count();
            foreach($upcomingShifts as $shift){
                $startDate = Carbon::parse($shift->start_date);
                $endDate = Carbon::parse($shift->end_date);
                $startTime = Carbon::parse($shift->start_time);
                $endTime = Carbon::parse($shift->end_time);

                $timeDifferenceInMinutes = calculateTimeDifferenceInMinutes($startTime, $endTime);
                $totalWorkingHour += $timeDifferenceInMinutes / 60;

                if($shift->shift_type == 1){
                    $startDateTime = Carbon::parse($shift->end_date)->subDays(1)->setTimeFrom($shift->start_time);
                    $endDateTime = Carbon::parse($shift->end_date.' '.$shift->end_time);
                } else {
                    $startDateTime = Carbon::parse($shift->end_date.' '. $shift->start_time);
                    $endDateTime = Carbon::parse($shift->end_date.' '.$shift->end_time);
                }
    
                $currentDateTime = Carbon::now();
                
                $lastShift = false;
                if ($currentDateTime->between($startDateTime, $endDateTime) || $currentDateTime->gt($endDateTime)) {
                    $lastShift = true;
                }

                $shiftsData['records'][] = [
                    'shift_id'          => $shift->id,
                    'shift_label'       => $shift->shift_label,
                    'sub_admin_name'    => $shift->client->name,
                    'company_address'   => $shift->clientDetail->address,
                    'occupation_name'   => $shift->occupation->name,
                    'location_name'     => $shift->location->name,
                    'start_date'        => $shift->start_date,
                    'end_date'          => $shift->end_date,
                    'start_time'        => $shift->start_time,
                    'end_time'          => $shift->end_time,
                    'check_in_status'   => $shift->clockInOuts()->where('clockin_date','<',Carbon::now())->exists(),
                    'check_out_status'  => $shift->clockInOuts()->where('clockout_date','<',Carbon::now())->exists(),
                    'is_last_shift'     => $lastShift,
                ];
            }
            $shiftsData['total_hours'] = number_format($totalWorkingHour,2);

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.record_retrieved_successfully'),
                'data'      => $shiftsData,
            ])->setStatusCode(Response::HTTP_OK);
        } 
        catch(\Exception $e){
            // dd($e->getMessage().'->'.$e->getLine());
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function pickShift(Request $request){
        $user = auth()->user();
        $request->validate([
            'id' => ['required', 'exists:shifts,id,deleted_at,NULL', function ($attribute, $value, $fail) use($user) {
                $selectedShift = Shift::find($value);
                if($selectedShift->status != 'open'){
                    $fail('The shift already picked');
                }

                $isShiftWithinAssignedShifts = $user->assignShifts()
                ->whereIn('status', ['picked','complete'])
                ->where(function ($query) use($selectedShift) {
                    $query->where(function ($q)  use($selectedShift) {
                        $q->whereBetween('start_date', [$selectedShift->start_date, $selectedShift->end_date])
                          ->orWhereBetween('end_date', [$selectedShift->start_date, $selectedShift->end_date]);
                    })
                    ->where(function ($q)  use($selectedShift) {
                        $q->where('start_time', '>=', $selectedShift->start_time)
                          ->where('end_time', '<=', $selectedShift->end_time);
                    });
                })
                ->exists();

                if($isShiftWithinAssignedShifts){
                    $fail("The shift's time slot overlaps with your assigned shifts.");
                } 
            }]
        ]);

        if(isset($request->assign_staff)){
            $startTime = $input['start_time']; 
            $endTime   = $input['end_time']; 

            $startDate   = $input['start_date']; 
            $endDate     = $input['end_date']; 

            $assignStaff = User::where('uuid', $request->assign_staff)->first();
            $conflictingShifts = $assignStaff->assignShifts()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhereRaw('? BETWEEN start_time AND end_time', [$startTime])
                    ->orWhereRaw('? BETWEEN start_time AND end_time', [$endTime]);
            })
            ->where('end_date', '>=', DB::raw('CURDATE()'))
            ->where('status','picked')
            ->whereNull('cancel_at')
            ->get();

           if($conflictingShifts->count() > 0){
                $response = [
                    'success' => false,
                    'error_type' => 'something_error',
                    'error' => 'Staff is not available for the selected time slot. Please choose a different time slot.',
                ];
                return response()->json($response,422);

           }
        }

        DB::beginTransaction();
        try {
            

            $shift = Shift::find($request->id);

            $shift->update([
                'picked_at' => date('Y-m-d H:i:s'),
                'status' => 'picked',
            ]);
            $shift->staffs()->sync([$user->id => ['created_at' => date('Y-m-d H:i:s')]]);

            $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
            $messageData = [
                'notification_type' => array_search(config('constant.subject_notification_type.shift_pickings'), config('constant.subject_notification_type')),
                'section'           => $key,
                'subject'           => trans('messages.shift.shift_picked_subject'),
                'message'           => trans('messages.shift.shift_picked_admin_message', [
                    'username'      => $user->name,
                    'picked_at'     => date('Y-m-d'),
                    'status'        => 'picked',
                ]),
            ];
            
            Notification::send($user, new SendNotification($messageData));
            
            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.shift_picked_success')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function clockInShift(Request $request){
        $user = auth()->user();
        $request->validate([
            'id'        => ['required', 'exists:shifts,id,deleted_at,NULL'],
            'latitude'  => ['required', 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)/'],
            'longitude' => ['required', 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)/'],
            // 'clockin_address'   => ['required', 'string'],
        ], [], [
            'latitude.regex' => 'The latitude is not correct',
            'longitude.regex' => 'The longitude is not correct',
        ]);

        DB::beginTransaction();
        try {
            ClockInOut::create([
                'user_id'           => $user->id,
                'shift_id'          => $request->id,
                'clockin_date'      => date('Y-m-d H:i:s'),
                'clockin_latitude'  => $request->latitude,
                'clockin_longitude' => $request->longitude
            ]);
            $shift =  Shift::find($request->id);
            $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
            $messageData = [
                'notification_type' => array_search(config('constant.subject_notification_type.clock_in'), config('constant.subject_notification_type')),
                'section'           => $key,
                'subject'           => trans('messages.shift.shift_clock_in_subject'),
                'message'           => trans('messages.shift.shift_clock_in_admin_message', [
                    'username'      => $user->name,
                    'clockin_date'  => date('Y-m-d'),
                    'clockin_time'  => $shift->start_time.' to '.$shift->end_time,
                ]),
            ];
            
            Notification::send($user, new SendNotification($messageData));
            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.clock_in_success')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function clockOutShift(Request $request){
        $user = auth()->user();
        $request->validate([
            'id'        => ['required', 'exists:shifts,id,deleted_at,NULL'],
            'latitude'  => ['required', 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)/'],
            'longitude' => ['required', 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)/'],
            // 'clockin_address'   => ['required', 'string'],
        ], [], [
            'latitude.regex' => 'The latitude is not correct',
            'longitude.regex' => 'The longitude is not correct',
        ]);

        DB::beginTransaction();
        try {
            $shift =  Shift::find($request->id);

            $clockOut = ClockInOut::where('user_id', $user->id)->where('shift_id', $shift->id)->latest()->first();
            $clockOut->update([
                'clockout_date'      => date('Y-m-d H:i:s'),
                'clockout_latitude'  => $request->latitude,
                'clockout_longitude' => $request->longitude
            ]);

            if($shift->shift_type == 1){
                $startDateTime = Carbon::parse($shift->end_date)->subDays(1)->setTimeFrom($shift->start_time);
                $endDateTime = Carbon::parse($shift->end_date.' '.$shift->end_time);
            } else {
                $startDateTime = Carbon::parse($shift->end_date.' '. $shift->start_time);
                $endDateTime = Carbon::parse($shift->end_date.' '.$shift->end_time);
            }

            $currentDateTime = Carbon::now();

            if ($currentDateTime->between($startDateTime, $endDateTime) || $currentDateTime->gt($endDateTime)) {
                $shift->status = 'complete';
                if($request->has('type') && $request->type == 'complete'){
                    $shift->is_authorized = 1;
                }
                $shift->save();

                $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
                $messageData = [
                    'notification_type' => array_search(config('constant.subject_notification_type.clock_out'), config('constant.subject_notification_type')),
                    'section'           => $key,
                    'subject'           => trans('messages.shift.shift_clock_out_subject'),
                    'message'           => trans('messages.shift.shift_clock_out_admin_message', [
                        'username'      => $user->name,
                        'clockout_date' => date('Y-m-d'),
                        'clockout_time' => $shift->start_time.' to '.$shift->end_time,
                    ]),
                ];
                
                Notification::send($user, new SendNotification($messageData));
            }

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.clock_out_success')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function cancelShift(Request $request) {
        $user = auth()->user();
        $request->validate([
            'id' => ['required', 'exists:shifts,id,deleted_at,NULL']
        ]);

        DB::beginTransaction();
        try {
            $shift = Shift::find($request->id);

            $currentDate = Carbon::now()->toDateString();
            $currentDateTime  = Carbon::now();
            
            $endDateTime = Carbon::parse($shift->end_date . ' ' . $shift->end_time);
            if ($currentDateTime->gt($endDateTime)) {
                Shift::where('id', $request->id)->update(['status' => 'cancel', 'cancel_at' => date('Y-m-d H:i:s')]);
            }else{
                Shift::where('id', $request->id)->update(['status' => 'open', 'cancel_at' => Null]);
            }

            $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
            $messageData = [
                'notification_type' => array_search(config('constant.subject_notification_type.shift_cancels'), config('constant.subject_notification_type')),
                'section'           => $key,
                'subject'           => trans('messages.shift.shift_canceled_subject'),
                'message'           => trans('messages.shift.shift_canceled_admin_message', [
                    'username'      => $user->name,
                    'status'        => 'canceled',
                    'start_date'    => Carbon::parse($shift->start_date)->format('d-m-Y'),
                    'start_time'    => Carbon::parse($shift->start_time)->format('H:i'),
                    'cancel_at'     => date('d-m-Y H:i'),
                ]),
            ];
            
            Notification::send($user, new SendNotification($messageData));

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.shift_cancelled')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function authorizedSign(Request $request){
        $user = auth()->user();
        $request->validate([
            'id'         => ['required', 'exists:shifts,id,deleted_at,NULL'],
            'full_name'  => ['required', 'string', new NoMultipleSpacesRule],
            'signature'  => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $authSign = AuthorizedShift::create([
                'user_id'           => $user->id,
                'shift_id'          => $request->id,
                'manager_name'      => $request->full_name,
                'authorize_at'      => date('Y-m-d H:i:s')
            ]);

            $shift = Shift::where('id', $request->id)->first();
            if($authSign && $request->has('signature')){
                Shift::where('id', $request->id)->update(['is_authorized' => 1]);
                uploadImage($authSign, $request->signature, 'shifts/authorize-signature',"authorize-signature", 'original', 'save', null);
            }

            $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
            $messageData = [
                'notification_type' => array_search(config('constant.subject_notification_type.authorised_sign'), config('constant.subject_notification_type')),
                'section'           => $key,
                'subject'           => trans('messages.shift.shift_authorised_sign_subject'),
                'message'           => trans('messages.shift.shift_authorised_sign_admin_message', [
                    'username'      => $user->name,
                    'manager_name'  => $request->full_name,
                    'authorize_at'  => date('Y-m-d'),
                    'authorize_time'=> $shift->start_time.' to '.$shift->end_time,
                ]),
            ];
            
            Notification::send($user, new SendNotification($messageData));

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.authorized_shift_success')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }   

    public function shiftsForCurrentAndPreviousMonth(Request $request){
        try {          
            $user = auth()->user();
            $currentMonth = date('m');
            $previousMonth = date('m', strtotime('-1 month'));
        
            $shifts = $user->assignShifts()->with(['client', 'clientDetail','occupation','location'])->whereIn('status', ['complete', 'picked'])->where(function ($query) use ($currentMonth, $previousMonth) {
                $query->whereMonth('start_date', $currentMonth)
                    ->orWhere(function ($query) use ($previousMonth) {
                        $query->whereMonth('start_date', $previousMonth)
                            ->whereYear('start_date', date('Y'));
                    });
            })->orderBy('id', 'desc')->get();

            $shiftsData = [];
            $totalWorkingHour = 0;
            foreach ($shifts as $shift) {
                $startDate = Carbon::parse($shift->start_date);
                $endDate = Carbon::parse($shift->end_date);
                $startTime = Carbon::parse($shift->start_time);
                $endTime = Carbon::parse($shift->end_time);

                $daysDiff = $endDate->diffInDays($startDate) + 1;

                $workingHoursPerDay = $startTime->diffInHours($endTime);
                $totalWorkingHour += $workingHoursPerDay * $daysDiff;

                $shiftsData[] = [
                    'shift_id'          => $shift->id,
                    'shift_label'       => $shift->shift_label,
                    'sub_admin_name'    => $shift->client ? $shift->client->name : null,
                    'company_address'   => $shift->clientDetail ? $shift->clientDetail->address : null,
                    'occupation_name'   => $shift->occupation ? $shift->occupation->name : null,
                    'location_name'     => $shift->location ? $shift->location->name : null,
                    'start_date'        => $shift->start_date,
                    'end_date'          => $shift->end_date,
                    'start_time'        => $shift->start_time,
                    'end_time'          => $shift->end_time,
                    'type'              => $shift->status,
                ];
            }
            /*
            $shiftsData['extracolumn'] = [
                'total_hours' => $totalWorkingHour,
                'total_count' => $shifts->count(),
            ];
            */
            $shiftsData = array_values($shiftsData);
            
            return $this->respondOk([
                'status'   => true,
                'message'  => trans('cruds.shift.title').', '.trans('messages.record_retrieved_successfully'),
                'data'     => $shiftsData, 
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }
}