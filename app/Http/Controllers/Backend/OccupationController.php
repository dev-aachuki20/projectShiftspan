<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\OccupationDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Occupation\OccupationRequest;
use App\Models\Occupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class OccupationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(OccupationDataTable $dataTable)
    {
        abort_if(Gate::denies('occupation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('admin.occupation.index');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('occupation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            if($request->ajax()) {
                $viewHTML = view('admin.occupation.create')->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            }
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        } catch (\Exception $e) {
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OccupationRequest $request)
    {
        abort_if(Gate::denies('occupation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            if($request->ajax()) {
                $store = Occupation::create($request->all());
                if($store){
                    DB::commit();
                    return response()->json(['success' => true, 'message' => trans('cruds.occupation.title_singular').' '.trans('messages.crud.add_record')]);
                }
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        abort_if(Gate::denies('occupation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            if($request->ajax()) {
                $occupation = Occupation::where('uuid', $id)->first();
                $viewHTML = view('admin.occupation.edit', compact('occupation'))->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));   
            }
        }catch (\Exception $e) {
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(OccupationRequest $request, $id)
    {
        abort_if(Gate::denies('occupation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            if($request->ajax()) {       
                $update = Occupation::where('uuid', $id)->update(['name' => $request['name']]);

                if($update){
                    DB::commit();
                    return response()->json(['success' => true, 'message' => trans('cruds.occupation.title_singular').' '.trans('messages.crud.update_record')]);
                }
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('occupation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            if($request->ajax()) {       
                $delete = Occupation::where('uuid', $id)->first();
                
                if($delete->delete()) {
                    return response()->json(['success' => true, 'message' => trans('cruds.occupation.title_singular').' '.trans('messages.crud.delete_record')]);
                }
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    public function deleteMultipleOccupation(OccupationRequest $request){
        abort_if(Gate::denies('occupation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            if($request->ajax()){
                $ids = $request->input('ids');
                $delete = Occupation::whereIn('uuid', $ids)->delete();
                if($delete) {
                    return response()->json(['success' => true, 'message' => trans('messages.crud.delete_record')]);
                }
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }
}
