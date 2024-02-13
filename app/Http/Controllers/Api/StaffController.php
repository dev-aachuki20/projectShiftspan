<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\TitleValidationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function updateProfile(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required','string','max:150',new TitleValidationRule],
            'email'    => ['required','email:dns'/*,'unique:users,email,NULL,id'*/],
            'address'   => ['required','string','max:255'],
            'phone' => ['required','digits:10','numeric','unique:users,phone,'.$user->id],
            'sub_admin_id'=> ['required','numeric','exists:users,id'],
            'occupation_id'=> ['required','numeric','exists:occupations,id'],
            'user_profile' => ['nullable','file','max:2048','mimes:jpeg,png'],
        ]);

        try{
            DB::beginTransaction();
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'sub_admin_id' => $request->sub_admin_id,
            ]);

            $user->profile()->update([
                'occupation_id' => $request->occupation_id,
                'address' => $request->address,
            ]);

            $actionType = 'save';
            $uploadId = null;
            if($profileImageRecord = $user->profileImage){
                $uploadId = $profileImageRecord->id;
                $actionType = 'update';
            }

            $request->hasFile('user_profile') ? uploadImage($user, $request->file('user_profile'), 'user/profile-images', "user_profile", 'original', $actionType, $uploadId) : null;
            $detailUser = User::find($user->id);
            DB::commit();

            return response()->json([
                'status'            => true,
                'message'           => trans('messages.success'),
                'userData'          => [
                    'id'           => $detailUser->id,
                    'name'   => $detailUser->name ?? null,
                    'phone'   => $detailUser->phone ?? null,
                    'email'    => $detailUser->email ?? null,
                    'address'   => $detailUser->profile->address ?? null,
                    'occupation_name'    => $detailUser->profile->occupation->name ?? null,
                    'company_name'    => $detailUser->company->name ?? null,
                    'profile_image'=> $detailUser->profile_image_url ? $detailUser->profile_image_url : asset(config('app.default.staff-image')),
                ]
            ], 200);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ], 500);
        }



    }
}
