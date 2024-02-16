<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\LocationDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreRequest;
use App\Http\Requests\Location\UpdateRequest;
use App\Models\Location;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LocationDataTable $dataTable)
    {
        abort_if(Gate::denies('location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('admin.location.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                $viewHTML = view('admin.location.create')->render();
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
        abort_if(Gate::denies('location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) 
        {
            DB::beginTransaction();
            try{
                Location::create($request->all());
                
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        if($request->ajax()) {        
            try{    
                $location = Location::where('uuid', $id)->first();
                $viewHTML = view('admin.location.edit', compact('location'))->render();
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
    public function update(UpdateRequest $request, $id)
    {
        abort_if(Gate::denies('location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        if ($request->ajax()) 
        {
            $location = Location::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                $location->update($request->all());
                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('cruds.location.title_singular').' '.trans('messages.crud.update_record'),
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
            $location = Location::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                $location->delete();
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
                'ids.*' => 'exists:locations,uuid',
            ]);

            if (!$validator->passes()) {
                return response()->json(['success'=>false,'errors'=>$validator->getMessageBag()->toArray(),'message'=>trans('messages.error_message')],400);
            }else{
                DB::beginTransaction();
                try {
                    $ids = $request->input('ids');
                    Location::whereIn('uuid', $ids)->delete();
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
