<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\DataTables\UserDataTable;
use App\DataTables\UserTypeDataTable;
use App\Exports\UserExport;
use App\Http\Requests\Staff\StaffCreateRequest;
use App\Http\Requests\Staff\StaffUpdateRequest;
use App\Models\Address;
use App\Models\Role;
use App\Models\User;
use App\Rules\MatchOldPassword;
use App\Rules\TitleValidationRule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    public function showprofile(){
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function updateprofile(Request $request){        
        $user = auth()->user();
        $request->validate([
            'name'  => ['required'],
            'profile_image'  =>['nullable', 'image', 'max:'.config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'phone' => [
                'required',
                'integer',
                'regex:/^[0-9]{7,15}$/',
                'not_in:-',
                'unique:users,phone,'. $user->id.',id,deleted_at,NULL',
            ],
        ],[
            'phone.required'=>'The phone number field is required',
            'phone.regex' =>'The phone number length must be 7 to 15 digits.',
            'phone.unique' =>'The phone number already exists.',
            'profile_image.image' =>'Please upload image.',
            'profile_image.mimes' =>'Please upload image with extentions: jpeg,png,jpg.',
            'profile_image.max' =>'The image size must equal or less than 2MB',
        ]);
        if($request->ajax()){
            DB::beginTransaction();
            try {            
                $user->update($request->all());

                if($request->has('profile_image')){
                    $uploadId = null;
                    $actionType = 'save';
                    if($profileImageRecord = $user->profileImage){
                        $uploadId = $profileImageRecord->id;
                        $actionType = 'update';
                    }
                    uploadImage($user, $request->profile_image, 'user/profile-images',"user_profile", 'original', $actionType, $uploadId);
                }
                DB::commit();

                $user = User::where('id', $user->id)->first();
                $data = [
                    'success' => true,
                    'profile_image' => $user->profile_image_url,
                    'auth_name' => $user->name,
                    'message' => trans('messages.crud.update_record'),
                ];
                return response()->json($data, 200);
            } catch (\Exception $e) {
                DB::rollBack();                
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    public function updateprofileImage(Request $request){
        $request->validate([
            'profile_image' => 'image|max:1024|mimes:jpeg,png,gif',
        ]);
        $user = auth()->user();
        $actionType = 'save';
        $uploadId = null;
        if($profileImageRecord = $user->profileImage){
            $uploadId = $profileImageRecord->id;
            $actionType = 'update';
        }

        $response = uploadImage($user, $request->profile_image, 'user/profile-images',"profile", 'original', $actionType, $uploadId);
        return response()->json(['success' => true,
        'message' => trans('messages.crud.update_record'),
        'title'=> trans('quickadmin.profile.profile'),
        'alert-type'=> trans('quickadmin.alert-type.success')
        ], 200);
    }

    public function showChangePassword(){
        return view('admin.change-password');
    }

    public function updateChangePassword(Request $request){
        $user = auth()->user();
        $request->validate([
            'currentpassword'  => ['required', 'string','min:8',new MatchOldPassword],
            'password'   => ['required', 'string', 'min:8', 'different:currentpassword'],
            'password_confirmation' => ['required','min:8','same:password'],

        ], getCommonValidationRuleMsgs());
        if($request->ajax()){
            DB::beginTransaction();
            try {
                $user->update(['password'=> Hash::make($request->password)]);
                DB::commit();
                $data = [
                    'success' => true,
                    'message' => trans('passwords.reset'),
                ];
                return response()->json($data, 200);
            } catch (\Exception $e) {
                DB::rollBack();                
                return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
            }
        }
        return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('staff_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true,
         'message' => trans('messages.crud.delete_record'),
         'alert-type'=> trans('quickadmin.alert-type.success'),
         'title' => trans('quickadmin.users.users')
        ], 200);
    }
}
