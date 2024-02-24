<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\StaffDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(StaffDataTable $dataTable)
    {
        abort_if(Gate::denies('sub_admin_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('admin.staff.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
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
                    $locations = User::whereIn('uuid', $ids)->get();
                    
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

    public function updateStaffStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'exists:users,uuid',
        ]);

        if (!$validator->passes()) {
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }else{
            DB::beginTransaction();
            try {
                $user = User::where('uuid', $request->id)->first();

                $updateStatus = $user->is_active == 1 ? 0 : 1;
                
                $user->update(['is_active' => $updateStatus]);
                
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
    }
}
