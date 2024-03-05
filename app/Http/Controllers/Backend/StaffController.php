<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\StaffDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\NotificationRequest;
use App\Http\Requests\Staff\StaffRequest;
use App\Models\User;
use App\Models\Profile;
use App\Notifications\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Hash;
use Auth;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(StaffDataTable $dataTable)
    {
        abort_if(Gate::denies('staff_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = Auth::user();
            $staffsNotifify = '';
            if($user->roles->first()->name == 'Super Admin'){
                $staffsNotifify = User::whereNotIN('id',[1])->orderBy('id', 'desc')->get()->pluck('name', 'uuid');
            }else{
                $staffsNotifify = User::where('company_id', $user->id)->orderBy('id', 'desc')->get()->pluck('name', 'uuid');
            }
            
            return $dataTable->render('admin.staff.index', compact('staffsNotifify'));
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('staff_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                if(auth()->user()->is_super_admin){
                    $subAdmins = User::whereHas('roles', function($q){ $q->where('id', config('constant.roles.sub_admin')); })->pluck('name', 'uuid');
                    $viewHTML = view('admin.staff.create', compact('subAdmins'))->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
                }
                $viewHTML = view('admin.staff.create')->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } 
            catch (\Exception $e) {
                \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine().' '.$e->getCode());          
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StaffRequest $request)
    {
        abort_if(Gate::denies('staff_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            if ($request->ajax()){
                DB::beginTransaction();
                $input = $request->all();

                if(!(auth()->user()->is_super_admin)){
                    $input['company_id'] = auth()->user()->id;
                } else {
                    $input['company_id'] = User::where('uuid', $request->company_id)->first()->id;
                }
                
                // $input['username'] = $request->name;
                $input['password'] = Hash::make($request->password);

                $staff = User::create($input);
                $input['user_id'] = $staff->id;
                $staff->profile()->create($input);
                $staff->roles()->sync([config('constant.roles.staff')]);
                
                if($staff && $request->hasFile('image')){
                    uploadImage($staff, $request->image, 'user/profile-images',"user_profile", 'original', 'save', null);
                }

                if($staff && $request->has('relevant_training')){
                    uploadImage($staff, $request->relevant_training, 'staff/relevant-training',"user_training_doc", 'original', 'save', null);
                }

                if($staff && $request->has('dbs_certificate')){
                    uploadImage($staff, $request->dbs_certificate, 'staff/dbs-certificate',"user_dbs_certificate", 'original', 'save', null);
                }

                if($staff && $request->has('cv_image')){
                    uploadImage($staff, $request->cv_image, 'staff/cv-image',"user_cv", 'original', 'save', null);
                }

                if($staff && $request->has('staff_budge')){
                    uploadImage($staff, $request->image, 'staff/staff-budge',"user_staff_budge", 'original', 'save', null);
                }

                if($staff && $request->has('dbs_check')){
                    uploadImage($staff, $request->dbs_check, 'staff/dbs-check',"user_dbs_check", 'original', 'save', null);
                }

                if($staff && $request->has('training_check')){
                    uploadImage($staff, $request->training_check, 'staff/training-check',"user_training_check", 'original', 'save', null);
                }

                DB::commit();

                if($staff){
                    return response()->json([
                        'success' => true,
                        'message' => trans('cruds.staff.title_singular').' '.trans('messages.crud.add_record'),
                    ]);
                }
            }

            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getLine());          
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('staff_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                $users = User::where('uuid', $id)->first();
                $subAdmins = null;
                if(auth()->user()->is_super_admin){
                    $subAdmins = User::where('id', $users->company_id)->first();                    
                    $viewHTML = view('admin.staff.show', compact('users', 'subAdmins'))->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
                }
                $viewHTML = view('admin.staff.show', compact('users', 'subAdmins'))->render();
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
        abort_if(Gate::denies('staff_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            if($request->ajax()) {
                $staff = User::where('uuid', $id)->first();
                if(auth()->user()->is_super_admin){
                    $subAdmins = User::whereHas('roles', function($q){ $q->where('id', config('constant.roles.sub_admin')); })->pluck('name', 'id', 'uuid');
                    $viewHTML = view('admin.staff.edit', compact('staff', 'subAdmins'))->render();
                    return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
                }
                $viewHTML = view('admin.staff.edit', compact('staff'))->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            } 
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        } catch (\Exception $e) {
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getLine()); 
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StaffRequest $request, string $id)
    {
        abort_if(Gate::denies('staff_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            if ($request->ajax()){
                $user = User::where('uuid', $id)->first();
                DB::beginTransaction();
                $input = $request->validated();

                if(!(auth()->user()->is_super_admin)){
                    $input['company_id'] = auth()->user()->id;
                } else {
                    $input['company_id'] = User::where('id', $request->company_id)->first()->id;
                }

                $staff = $user->update($input);
                $profileData = $request->only([
                    'dob',
                    'previous_name',
                    'national_insurance_number',
                    'address',
                    'education',
                    'prev_emp_1',
                    'prev_emp_2',
                    'reference_1',
                    'reference_2',
                    'date_sign',
                    'is_criminal',
                    'is_rehabilite',
                    'is_enquire',
                    'is_health_issue',
                    'is_statement',
                ]);
                $profile = Profile::updateOrCreate(['user_id' => $user->id], $profileData);
                $user->roles()->sync([config('constant.roles.staff')]);
                
                if($user && $request->hasFile('image')){
                    $uploadImageId = $user->profileImage ? $user->profileImage->id : null;
                    uploadImage($user, $request->image, 'user/profile-images',"user_profile", 'original', $user->profileImage ? 'update' : 'save', $uploadImageId ? $uploadImageId : null);
                }

                if($user && $request->has('relevant_training')){
                    $uploadImageId = $user->trainingDocument ? $user->trainingDocument->id : null;
                    uploadImage($user, $request->relevant_training, 'staff/relevant-training',"user_training_doc", 'original', $user->trainingDocument ? 'update' : 'save', $uploadImageId ? $uploadImageId : null);
                }

                if($user && $request->has('dbs_certificate')){
                    $uploadImageId = $user->dbsCertificate ? $user->dbsCertificate->id : null;
                    uploadImage($user, $request->dbs_certificate, 'staff/dbs-certificate',"user_dbs_certificate", 'original', $user->dbsCertificate ? 'update' : 'save', $uploadImageId ? $uploadImageId : null);
                }
                
                if($user && $request->has('cv_image')){
                    $uploadImageId = $user->cv ? $user->cv->id : null;
                    uploadImage($user, $request->cv_image, 'staff/cv-image',"user_cv", 'original', $user->cv ? 'update' : 'save', $uploadImageId ? $uploadImageId : null);
                }
                
                if($user && $request->has('staff_budge')){
                    $uploadImageId = $user->staffBudge ? $user->staffBudge->id : null;
                    uploadImage($user, $request->image, 'staff/staff-budge',"user_staff_budge", 'original', $user->staffBudge ? 'update' : 'save', $uploadImageId ? $uploadImageId : null);
                }
                
                if ($user && $request->has('dbs_check')) {
                    $uploadImageId = $user->dbsCheck ? $user->dbsCheck->id : null;
                    uploadImage($user, $request->file('dbs_check'), 'staff/dbs-check', "user_dbs_check", 'original', $uploadImageId ? 'update' : 'save', $uploadImageId ?? null);
                }                
                
                if($user && $request->has('training_check')){
                    $uploadImageId = $user->trainingCheck ? $user->trainingCheck->id : null;
                    uploadImage($user, $request->training_check, 'staff/training-check',"user_training_check", 'original', $user->trainingCheck ? 'update' : 'save', $uploadImageId ? $uploadImageId : null);
                }

                DB::commit();

                if($user){
                    return response()->json([
                        'success' => true,
                        'message' => trans('cruds.staff.title_singular').' '.trans('messages.crud.update_record'),
                    ]);
                }
            }

            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getLine());          
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('staff_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $user = User::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                $user->delete();
                DB::commit();
                
                return response()->json($response = [
                    'success'    => true,
                    'message'    => trans('messages.crud.delete_record'),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();                
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    public function massDestroy(StaffRequest $request)
    {
        abort_if(Gate::denies('staff_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $ids = $request->input('ids');
                $users = User::whereIn('uuid', $ids)->delete();
                DB::commit();
                
                if($users){
                    return response()->json($response = [
                        'success'    => true,
                        'message'    => trans('messages.crud.delete_record'),
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();      
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
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

    /* Notification Store */
    public function notificationStore(NotificationRequest $request)
    {
        // abort_if(Gate::denies('notification_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $input = $request->validated();            
            $input['notification_type'] = 'send_notification';

            DB::beginTransaction();
            $users = User::whereIn('uuid', $input['staffs'])->get();
            Notification::send($users, new SendNotification($input));

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.crud.add_record'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            return response()->json([
                'success' => false, 
                'error_type' => 'something_error', 
                'error' => trans('messages.error_message')
            ], 400 );
        }
        
    }
}
