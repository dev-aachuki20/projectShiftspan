<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\SubAdminDetailDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubAdminDetail\StoreRequest;
use App\Http\Requests\SubAdminDetail\UpdateRequest;
use App\Models\ClientDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SubAdminDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SubAdminDetailDataTable $dataTable)
    {
        abort_if(Gate::denies('sub_admin_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try{
            return $dataTable->render('admin.sub_admin_detail.index');
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
        abort_if(Gate::denies('sub_admin_detail_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                if(auth()->user()->is_super_admin){
                    $subAdmins = User::whereHas('roles', function($q){ $q->where('id', config('constant.roles.sub_admin')); })->pluck('name', 'uuid');
                    // dd($subAdmins);
                    $viewHTML = view('admin.sub_admin_detail.create', compact('subAdmins'))->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
                }
                $viewHTML = view('admin.sub_admin_detail.create')->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } 
            catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('sub_admin_detail_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) 
        {
            DB::beginTransaction();
            try{
                $input = $request->all();
                if(!(auth()->user()->is_super_admin)){
                    $input['sub_admin_id'] = auth()->user()->id;
                } else {
                    $input['sub_admin_id'] = User::where('uuid', $request->sub_admin_id)->first()->id;
                }
                $subAdminDetail = ClientDetail::create($input);

                if($subAdminDetail && $request->has('building_image')){
                    uploadImage($subAdminDetail, $request->building_image, 'client-detail/building-images',"client-building-image", 'original', 'save', null);
                }

                DB::commit();

                $response = [
                    'success' => true,
                    'message' => trans('cruds.location.title_singular').' '.trans('messages.crud.add_record'),
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
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('sub_admin_detail_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                $subAdminDetail = ClientDetail::where('uuid', $id)->first();
                if(auth()->user()->is_super_admin){                    
                    $viewHTML = view('admin.sub_admin_detail.show', compact('subAdminDetail'))->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
                }
                $viewHTML = view('admin.sub_admin_detail.show', compact('subAdminDetail'))->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } 
            catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        abort_if(Gate::denies('sub_admin_detail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                $subAdminDetail = ClientDetail::where('uuid', $id)->first();
                if(auth()->user()->is_super_admin){
                    $subAdmins = User::whereHas('roles', function($q){ $q->where('id', config('constant.roles.sub_admin')); })->pluck('name', 'uuid');
                    
                    $viewHTML = view('admin.sub_admin_detail.edit', compact('subAdmins', 'subAdminDetail'))->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
                }
                $viewHTML = view('admin.sub_admin_detail.edit', compact('subAdminDetail'))->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } 
            catch (\Exception $e) {
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
        abort_if(Gate::denies('sub_admin_detail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) 
        {
            DB::beginTransaction();
            try{
                $subAdminDetail = ClientDetail::where('uuid', $id)->first();
                $input = $request->all();
                if(!(auth()->user()->is_super_admin)){
                    $input['sub_admin_id'] = auth()->user()->id;
                } else {
                    $input['sub_admin_id'] = User::where('uuid', $request->sub_admin_id)->first()->id;
                }
                $subAdminDetail->update($input);

                if($subAdminDetail && $request->has('building_image')){
                    $uploadId = null;
                    $actionType = 'save';
                    if($profileImageRecord = $subAdminDetail->buildingImage){
                        $uploadId = $profileImageRecord->id;
                        $actionType = 'update';
                    }
                    uploadImage($subAdminDetail, $request->building_image, 'client-detail/building-images',"client-building-image", 'original', $actionType, $uploadId);
                }
                DB::commit();

                $response = [
                    'success' => true,
                    'message' => trans('cruds.location.title_singular').' '.trans('messages.crud.update_record'),
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
        abort_if(Gate::denies('location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $subAdminDetail = ClientDetail::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                $subAdminDetail->delete();
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
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'ids'   => 'required|array',
                'ids.*' => 'exists:client_details,uuid',
            ]);

            if (!$validator->passes()) {
                return response()->json(['success'=>false,'errors'=>$validator->getMessageBag()->toArray(),'message'=>trans('messages.error_message')],400);
            }else{
                DB::beginTransaction();
                try {
                    $ids = $request->input('ids');
                    $locations = ClientDetail::whereIn('uuid', $ids)->delete();
                    
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
}
