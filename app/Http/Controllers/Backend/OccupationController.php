<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\OccupationDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Occupation\OccupationRequest;
use App\Models\Occupation;
use App\Models\User;
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
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('occupation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try {
                if((auth()->user()->is_super_admin)){
                    $subAdmins = User::whereHas('roles', function($q){ $q->where('id', config('constant.roles.sub_admin')); })->pluck('name', 'uuid');
                    $viewHTML = view('admin.occupation.create', compact('subAdmins'))->render();
                } else {
                    $assignedOccupationIds = auth()->user()->occupations()->pluck('occupations.id')->toArray();
                    $occupations = Occupation::whereNotIn('id', $assignedOccupationIds)->get()->pluck('name', 'uuid');
                    $viewHTML = view('admin.occupation.create', compact('occupations'))->render();
                }
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } catch (\Exception $e) {
                // dd($e);
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OccupationRequest $request)
    {
        abort_if(Gate::denies('occupation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            DB::beginTransaction();
            try {
                if((auth()->user()->is_super_admin)){
                    $occupation = Occupation::create($request->all());

                    if($occupation && !empty($request->sub_admin)){
                        $subAdminUsers = User::whereIn('uuid', $request->sub_admin)->pluck('id');
                        $occupation->subAdmins()->sync($subAdminUsers);
                    }
                } else {
                    if($request->has('save_type') && $request->save_type == 'new_occupation'){
                        $occupation = Occupation::create($request->all());
                    } else {
                        $occupation = Occupation::where('uuid', $request->occupation_name)->first();
                    }
                    if($occupation){
                        $occupation->subAdmins()->attach(auth()->user()->id);
                    }
                }
                DB::commit();
                
                return response()->json(['success' => true, 'message' => trans('cruds.occupation.title_singular').' '.trans('messages.crud.add_record')]);                
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
        abort_if(Gate::denies('occupation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try {
                if((auth()->user()->is_super_admin)){
                    $occupation = Occupation::where('uuid', $id)->first();
                    $subAdmins = User::whereHas('roles', function($q){ $q->where('id', config('constant.roles.sub_admin'));})->pluck('name', 'uuid');
                    $selectedSubAdmins = $occupation->subAdmins()->pluck('uuid')->toArray();
                    $viewHTML = view('admin.occupation.edit', compact('occupation', 'subAdmins', 'selectedSubAdmins'))->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));   
                }
                return response()->json(['success' => true, 'message' => trans('cruds.occupation.title_singular').' '.trans('messages.crud.update_record')]);
            } catch (\Exception $e) {
                // dd($e);
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(OccupationRequest $request, $id)
    {
        abort_if(Gate::denies('occupation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        if($request->ajax()) {       
            $occupation = Occupation::where('uuid', $id)->first();
            try {
                if((auth()->user()->is_super_admin)){
                    $occupation->update($request->all());

                    if($occupation && !empty($request->sub_admin)){
                        $subAdminUsers = User::whereIn('uuid', $request->sub_admin)->pluck('id');
                        $occupation->subAdmins()->sync($subAdminUsers);
                    } else {
                        $occupation->subAdmins()->sync([]);
                    }
                    DB::commit();
                    return response()->json(['success' => true, 'message' => trans('cruds.occupation.title_singular').' '.trans('messages.crud.update_record')]);
                }
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
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
        abort_if(Gate::denies('occupation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try {
                $occupation = Occupation::where('uuid', $id)->first();
                if((auth()->user()->is_super_admin)){
                    $occupation->subAdmins()->sync([]);
                    $occupation->delete();
                } else {
                    $occupation->subAdmins()->detach(auth()->user()->id);
                }
                
                return response()->json(['success' => true, 'message' => trans('cruds.occupation.title_singular').' '.trans('messages.crud.delete_record')]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    public function deleteMultipleOccupation(OccupationRequest $request){
        abort_if(Gate::denies('occupation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()){
            try {
                $ids = $request->input('ids');
                $occupations = Occupation::whereIn('uuid', $ids)->get();
                foreach($occupations as $occupation){
                    if((auth()->user()->is_super_admin)){
                        $occupation->subAdmins()->sync([]);
                        $occupation->delete();
                    } else {
                        $occupation->subAdmins()->detach(auth()->user()->id);
                    }
                }
                return response()->json(['success' => true, 'message' => trans('messages.crud.delete_record')]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }
}
