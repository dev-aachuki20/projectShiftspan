<?php

namespace App\Http\Controllers\Api;

use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ClockInOut;

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
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function completedShifts(){
        try{
            $user = auth()->user();

            $completedShifts = $user->assignShifts()->with(['client', 'clientDetail'])
            ->select('id', 'sub_admin_id', 'client_detail_id', 'start_date', 'start_time', 'end_date', 'end_time')->whereStatus('complete')
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
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }
    
    public function upcomingShifts(){
        try{
            $currentDateTime = Carbon::now();
            
            $user = auth()->user();

            $completedShifts = $user->assignShifts()->with(['client', 'clientDetail'])
            ->select('id', 'sub_admin_id', 'client_detail_id', 'start_date', 'start_time', 'end_date', 'end_time')->whereStatus('picked')
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
            $clockOut = ClockInOut::where('user_id', $user->id)->where('shift_id', $request->id)->latest()->first();
            $clockOut->update([
                'clockout_date'      => date('Y-m-d H:i:s'),
                'clockout_latitude'  => $request->latitude,
                'clockout_longitude' => $request->longitude
            ]);

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
}