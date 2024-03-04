<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
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

            $staffAssignedShifts = $user->assignShifts()->where(function ($query) use ($currentDateTime) {
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
            }

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

            ->where(function($query) use($startDateTimes, $request){
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
                foreach($startDateTimes as $dateData){
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
                }
            })        
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')            
            ->get();

            $shiftsData = [];
            
            $shiftsData = $companyShifts->map(function ($shift) {
                return [
                    'shift_id' => $shift->id,
                    'sub_admin_name' => $shift->client->name,
                    'company_name' => $shift->clientDetail->name,
                    'occupation_name' => $shift->occupation->name,
                    'start_date' => $shift->start_date,
                    'end_date' => $shift->end_date,
                    'start_time' => $shift->start_time,
                    'end_time' => $shift->end_time,
                    'build_image' => $shift->clientDetail->building_image_url ? $shift->clientDetail->building_image_url : asset(config('constant.default.building-image')),
                ];
            });
            $shiftsData['count'] = $shiftsData->count();

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
}