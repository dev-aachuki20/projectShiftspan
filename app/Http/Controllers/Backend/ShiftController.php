<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\ShiftDataTable;
use App\Http\Controllers\Controller;
use App\Models\ClientDetail;
use App\Models\Location;
use App\Models\Occupation;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('shift_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) 
        {
            DB::beginTransaction();
            try{
                $input = $this->modifyRequestInput($request);
                if($request->has('quantity')){
                    for($i=1;$i<=$request->quantity; $i++){
                        $shift = Shift::create($input);

                        if($i == 1 && $shift && $request->has('assign_staff') && !empty($request->assign_staff)){
                            $shift->update([
                                'picked_at' => date('Y-m-d H:i:s'),
                                'status' => 'picked',
                            ]);
                            
                            $staffId = User::where('uuid', $request->assign_staff)->first()->id;
                            $shift->staffs()->sync([$staffId => ['created_at' => date('Y-m-d H:i:s')]]);
                        }
                    }
                }
                DB::commit();
                $response = [
                    'success' => true,
                    'message' => trans('cruds.location.title_singular').' '.trans('messages.crud.add_record'),
                ];
                return response()->json($response);
            } catch (\Exception $e) {
                dd($e);
                DB::rollBack();                
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
                $shift = Shift::where('uuid', $id)->first();
                if(auth()->user()->is_super_admin){
                    $subAdmins = User::whereHas('roles', function($q){ $q->where('id', config('constant.roles.sub_admin')); })->pluck('name', 'uuid');
                    $selectedStaffs = $shift->staffs()->pluck('uuid')->toArray();
                    $compactData = array_merge(compact('subAdmins', 'shift', 'selectedStaffs'), $this->getShiftViewData($shift->client->uuid));

                    $viewHTML = view('admin.shift.edit', $compactData)->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
                }

                // for sub admin login
                $compactData = array_merge(compact('shift'), $this->getShiftViewData(auth()->user()->uuid));
                $viewHTML = view('admin.shift.edit', $compactData)->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } 
            catch (\Exception $e) {
                dd($e);
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
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
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('shift_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $shift = Shift::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                if ($shift->staffs) {
                    $shift->staffs()->sync([]);
                }
                $shift->delete();
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
                    $shfits = Shift::whereIn('uuid', $ids)->get();
                    foreach($shfits as $shift){
                        if ($shift->staffs) {
                            $shift->staffs()->sync([]);
                        }
                        $shift->delete();
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

                    // locations options
                    $locationsOptionHtml = '<option value="">'.(trans('global.select').' '.trans('cruds.location.title_singular')).'</option>';
                    foreach($subData['locations'] as $locationKey => $location){
                        $locationsOptionHtml .= '<option value="'.$locationKey.'" >'.$location.'</option>';
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
                        'location_html' => $locationsOptionHtml,
                        'occupation_html' => $occupationsOptionHtml,
                        'client_detail_html' => $clientDetailsOptionHtml,
                    ];
                    return response()->json($response);
                } catch (\Exception $e) {
                    dd($e);
                    return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
                }
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    private function getShiftViewData($userId){
        $user = User::whereUuid($userId)->first();

        $locations = $user->locations()->pluck('name', 'uuid');
        $occupations = $user->occupations()->pluck('name', 'uuid');
        $staffs = $user->staffs()->pluck('name', 'uuid');
        $clientDetails = $user->clientDetails()->pluck('name', 'uuid');

        return [
            'staffs' => $staffs,
            'locations' => $locations,
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
        $input['location_id']       = Location::where('uuid', $req->location_id)->first()->id;
        $input['occupation_id']     = Occupation::where('uuid', $req->occupation_id)->first()->id;

        $input['start_date']        = date('Y-m-d', strtotime($req->start_date));
        $input['end_date']          = date('Y-m-d', strtotime($req->end_date));
        $input['start_time']        = date('H:i:s', strtotime($req->start_time));
        $input['end_time']          = date('H:i:s', strtotime($req->end_time));

        return $input;
    }

    public function CancelShift(Request $request, $id){

        if ($request->ajax()) {
            $shift = Shift::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                
                $shift->update(['status' => 'cancel', 'cancel_at' => date('Y-m-d H:i:s')]);

                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('messages.crud.status_update'),
                ];
                return response()->json($response);
            } catch (\Exception $e) {
                DB::rollBack();                
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    public function RateShift(){
        
    }
}
