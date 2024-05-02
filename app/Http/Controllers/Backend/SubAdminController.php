<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

use App\DataTables\SubAdminDataTable;
use App\Http\Requests\SubAdmin\StoreRequest;
use App\Http\Requests\SubAdmin\UpdateRequest;
use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SubAdminDataTable $dataTable)
    {
        abort_if(Gate::denies('sub_admin_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('admin.sub_admin.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('sub_admin_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                $viewHTML = view('admin.sub_admin.create')->render();
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
        abort_if(Gate::denies('sub_admin_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) 
        {
            DB::beginTransaction();
            try{
                $input = $request->all();

                $companyNumber = getCompanyNumber();

                $input['password'] = bcrypt($request->password);
                $input['company_number'] = $companyNumber;
                $input['email_verified_at'] = date('Y-m-d H:i:s');

                $subAdmin = User::create($input);

                if($subAdmin){
                    $subAdmin->roles()->sync([config('constant.roles.sub_admin')]);
                }
                
                DB::commit();

                $response = [
                    'success' => true,
                    'message' => trans('cruds.client_admin.title_singular').' '.trans('messages.crud.add_record'),
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
                $subAdmin = User::where('uuid', $id)->first();
                $viewHTML = view('admin.sub_admin.edit', compact('subAdmin'))->render();
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
        abort_if(Gate::denies('sub_admin_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        if ($request->ajax()) 
        {
            $subAdmin = User::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                $input = $request->except('_token', 'email');

                $subAdmin->update($input);

                DB::commit();

                return response()->json(['success' => true, 'message' => trans('cruds.client_admin.title_singular').' '.trans('messages.crud.update_record')]);                
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
        abort_if(Gate::denies('sub_admin_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $subAdmin = User::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                $this->deleteSubAdmin($subAdmin);                
                
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
        abort_if(Gate::denies('sub_admin_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'ids'   => 'required|array',
                'ids.*' => 'exists:users,uuid,deleted_at,NULL',
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }else{
                DB::beginTransaction();
                try {
                    $ids = $request->input('ids');
                    $subAdmins = User::whereIn('uuid', $ids)->get();
                    foreach($subAdmins as $subAdmin){
                        $this->deleteSubAdmin($subAdmin);
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

    private function deleteSubAdmin($subAdmin){
        if ($subAdmin->profile_image_url) {
            $uploadImageId = $subAdmin->profileImage->id;
            deleteFile($uploadImageId);
        }
        if($subAdmin->occupations){
            $subAdmin->occupations()->sync([]);
        }
        if($subAdmin->staffs){
            $subAdmin->staffs()->delete();
        }

        $groupIds = $subAdmin->groups()->pluck('id')->toArray();
        Message::whereIn('group_id', $groupIds)->delete();
        Group::whereIn('id', $groupIds)->delete();

        $subAdmin->delete();
    }

    public function statusUpdate(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'exists:users,uuid',
            ]);
    
            if (!$validator->passes()) {
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }else{
                DB::beginTransaction();
                $user = User::where('uuid', $request->id)->first();
                $updateStatus = $user->is_active == 1 ? 0 : 1;

                if($user->update(['is_active' => $updateStatus])){
                    DB::commit();
                    return response()->json([
                        'success'    => true,
                        'message'    => trans('messages.crud.status_update'),
                    ], 200);
                }

                return response()->json([
                    'success' => false, 
                    'error_type' => 'something_error', 
                    'error' => trans('messages.error_message')
                ], 400 );
            }
        } catch (\Exception $e) {
            DB::rollBack();                
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }
}