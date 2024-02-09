<?php

namespace App\Http\Controllers;

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
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    public function showprofile(){
        abort_if(Gate::denies('profile_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $addresses = Address::all();
        $user = auth()->user();
        return view('admin.profile.show', compact('addresses','user'));
    }

    public function updateprofile(Request $request){
        $user = auth()->user();
        $validatedData = $request->validate([
            'name' => ['required','string','unique:users,name,'.$user->id, new TitleValidationRule],
            'username' => ['required','string','max:40','unique:users,username,'.$user->id],
            // 'email' => ['required','email','unique:users,email,' . $user->id],
            'phone' => ['nullable','digits:10','numeric'],
            'address_id' => ['required','numeric'],
        ]);

        $user->update($validatedData);
        return response()->json(['success' => true,
        'message' => trans('messages.crud.update_record'),
        'title'=> trans('quickadmin.profile.profile'),
        'alert-type'=> trans('quickadmin.alert-type.success')
        ], 200);
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

    public function showchangepassform(){
        return view('admin.profile.change-password');
    }

    public function updatePassword(Request $request){
        $userId = auth()->user()->id;
        $validated = $request->validate([
            'currentpassword'  => ['required', 'string','min:4',new MatchOldPassword],
            'password'   => ['required', 'string', 'min:4','confirmed', 'different:currentpassword'],
            'password_confirmation' => ['required','min:4','same:password'],

        ], getCommonValidationRuleMsgs());
        User::find($userId)->update(['password'=> Hash::make($request->password)]);
        return redirect()->back()->with(['success' => true,
        'message' => trans('passwords.reset'),
        'title'=> trans('quickadmin.profile.fields.password'),
        'alert-type'=> trans('quickadmin.alert-type.success')]);
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
