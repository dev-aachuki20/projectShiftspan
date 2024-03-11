<?php

namespace App\Http\Controllers\Api;

use App\Models\AuthorizedShift;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ClockInOut;
use App\Rules\NoMultipleSpacesRule;

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

            /* $staffAssignedShifts = $user->assignShifts()->where(function ($query) use ($currentDateTime) {
                $query->whereDate('start_date', '>', $currentDateTime->toDateString())
                ->orWhere(function ($query) use ($currentDateTime) {
                    $query->whereDate('start_date', '=', $currentDateTime->toDateString())
                        ->whereTime('start_time', '>', $currentDateTime->toTimeString());
                });
            })->get();
            $startDateTimes = [];
            foreach($staffAssignedShifts as $key => $assignShift){
                $startDate  = $assignShift->start_date;
                $endDate    = $assignShift->end_date;

                $start      = Carbon::parse($startDate);
                $end        = Carbon::parse($endDate);

                for ($date = $start; $date->lte($end); $date->addDay()) {
                    $startDateTimes[] = [ 'date' => $date->format('Y-m-d'), 'start_time' => $assignShift->start_time, 'end_time' => $assignShift->end_time, 'start_date' => $assignShift->start_date, 'end_date' => $assignShift->end_date]; 
                }
            } */

            $companyShifts = $user->company->companyShifts()->with(['client', 'clientDetail', 'occupation'])
            ->select('id', 'sub_admin_id', 'client_detail_id', 'location_id', 'occupation_id', 'start_date', 'start_time', 'end_date', 'end_time')

            ->whereStatus('open')

            ->where(function ($query) use ($currentDateTime) {
                $query->whereDate('start_date', '>', $currentDateTime->toDateString())
                ->orWhere(function ($query) use ($currentDateTime) {
                    $query->whereDate('start_date', '=', $currentDateTime->toDateString())
                        ->whereTime('start_time', '>', $currentDateTime->toTimeString());
                });
            })

            ->where(function($query) use($request){
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
                /* foreach($startDateTimes as $dateData){
                    $query->where(function($subquery) use($dateData){

                        $subquery->whereDate('start_date', '<=', $dateData['date'])                    
                            ->whereDate('end_date', '>=', $dateData['date'])

                            ->where(function($subsubquery) use($dateData){
                                $subsubquery->WhereTime('start_time', '>', $dateData['end_time'])
                                    ->orwhereTime('end_time', '<', $dateData['start_time']);
                            });
                    })
                    ->orWhere(function($q) use($dateData){
                        $q->where('start_date', '<', $dateData['start_date'])
                        ->orWhere('end_date', '>', $dateData['end_date']);
                    });
                } */
            })        
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')            
            ->get();

            $shiftsData = [];
            // $shiftsData['count'] = $companyShifts->count();
            foreach($companyShifts as $key => $shift){
                $shiftsData[] = [
                    'shift_id'          => $shift->id,
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
            ->select('id', 'sub_admin_id', 'client_detail_id','occupation_id','location_id',  'start_date', 'start_time', 'end_date', 'end_time','is_authorized')
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

                $daysDiff = $endDate->diffInDays($startDate) + 1;

                $workingHoursPerDay = $startTime->diffInHours($endTime);
                $totalWorkingHour += $workingHoursPerDay * $daysDiff;
                
                $shiftsData['records'][] = [
                    'shift_id'          => $shift->id,
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

            return $this->throwValidation([trans('messages.error_message')]);
        }
    }
    
    public function upcomingShifts(){
        try{
            $currentDateTime = Carbon::now();
            
            $user = auth()->user();

            $completedShifts = $user->assignShifts()->with(['client', 'clientDetail','occupation','location'])
            ->select('id', 'sub_admin_id', 'client_detail_id','occupation_id','location_id', 'start_date', 'start_time', 'end_date', 'end_time')->whereStatus('picked')
            ->where(function ($query) use ($currentDateTime) {
                $query->whereDate('start_date', '>', $currentDateTime->toDateString())
                ->orWhere(function ($query) use ($currentDateTime) {
                    $query->whereDate('start_date', '=', $currentDateTime->toDateString())
                        ->whereTime('start_time', '>', $currentDateTime->toTimeString());
                });
            })
            ->orderBy('start_date', 'desc')
            ->get();

            $shiftsData = [];
            $totalWorkingHour = 0;
            $shiftsData['count'] = $completedShifts->count();
            foreach($completedShifts as $shift){
                $startDate = Carbon::parse($shift->start_date);
                $endDate = Carbon::parse($shift->end_date);
                $startTime = Carbon::parse($shift->start_time);
                $endTime = Carbon::parse($shift->end_time);

                $daysDiff = $endDate->diffInDays($startDate) + 1;

                $workingHoursPerDay = $startTime->diffInHours($endTime);
                $totalWorkingHour += $workingHoursPerDay * $daysDiff;

                $shiftsData['records'][] = [
                    'shift_id'          => $shift->id,
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
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function pickShift(Request $request){
        $user = auth()->user();
        $currentDateTime = Carbon::now();
        $request->validate([
            'id' => ['required', 'exists:shifts,id,deleted_at,NULL', function ($attribute, $value, $fail) use($user, $currentDateTime) {
                $selectedShift = Shift::find($value);
                if($selectedShift->status != 'open'){
                    $fail('The shift already picked');
                }

                /* $isShiftWithinAssignedShifts = $user->assignShifts()
                ->where(function ($query) use ($selectedShift) {
                    $query->where(function ($subquery) use ($selectedShift) {
                        $subquery->where('start_date', '<=', $selectedShift->start_date)
                            ->where('end_date', '>=', $selectedShift->start_date)
                            ->where(function ($q) use ($selectedShift) {
                                $q->where('end_time', '>=', $selectedShift->start_time)
                                    ->orWhere('start_time', '<=', $selectedShift->start_time);
                            });
                    })
                    ->orWhere(function ($subquery) use ($selectedShift) {
                        $subquery->where('start_date', '<=', $selectedShift->end_date)
                            ->where('end_date', '>=', $selectedShift->end_date)
                            ->where(function ($q) use ($selectedShift) {
                                $q->where('end_time', '>=', $selectedShift->end_time)
                                    ->orWhere('start_time', '<=', $selectedShift->end_time);
                            });
                    });
                })
                ->exists();

                if($isShiftWithinAssignedShifts){
                    $fail("The shift's time slot overlaps with your assigned shifts.");
                } */
            }]
        ]);

        DB::beginTransaction();
        try {
            

            $shift = Shift::find($request->id);

            $shift->update([
                'picked_at' => date('Y-m-d H:i:s'),
                'status' => 'picked',
            ]);
            $shift->staffs()->sync([$user->id => ['created_at' => date('Y-m-d H:i:s')]]);

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.shift_picked_success')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
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

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.clock_in_success')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
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

            $endDate = Carbon::parse($shift->end_date)->format('Y-m-d');
            $currentDate = Carbon::now()->format('Y-m-d');

            if ($endDate == $currentDate) {

                $shift->status = 'complete';
                $shift->save();

            }

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.clock_out_success')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function cancelShift(Request $request) {
        // $user = auth()->user();
        $request->validate([
            'id' => ['required', 'exists:shifts,id,deleted_at,NULL']
        ]);

        DB::beginTransaction();
        try {
            $shift = Shift::where('id', $request->id)->first();
            
            $shift->update(['status' => 'cancel', 'cancel_at' => date('Y-m-d H:i:s')]);

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.shift_cancelled')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function authorizedSign(Request $request){
        $user = auth()->user();
        $request->validate([
            'id'        => ['required', 'exists:shifts,id,deleted_at,NULL'],
            'full_name'  => ['required', 'string', new NoMultipleSpacesRule],
            'signature' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $authSign = AuthorizedShift::create([
                'user_id'           => $user->id,
                'shift_id'          => $request->id,
                'manager_name'      => $request->full_name,
                'authorize_at'      => date('Y-m-d H:i:s')
            ]);

            if($authSign && $request->has('signature')){

                Shift::where('id',$request->id)->update(['is_authorized'=>1]);

                uploadImage($authSign, $request->signature, 'shifts/authorize-signature',"authorize-signature", 'original', 'save', null);
            }

            DB::commit();

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.authorized_shift_success')
            ])->setStatusCode(Response::HTTP_OK);
        }
        catch(\Exception $e){
            DB::rollBack();
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
            return $this->throwValidation([trans('messages.error_message'),$e->getMessage().' '.$e->getFile().' '.$e->getCode()]);
        }
    }
}