<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends APIController
{
    /**
     * Get user details.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function profile(){
        $user = auth()->user();
        return $this->respondOk([
            'status'        => true,
            'message'       => trans('messages.record_retrieved_successfully'),
            'data'          => [
                'id'        => $user->id,
                'uuid'      => $user->uuid,
                'name'      => $user->name,
                'phone'     => $user->phone,
                'email'     => $user->email,
                'address'   => $user->profile->address,
                'occupation_name'       => $user->profile->occupation->name ?? null,
                'occupation_id'         => $user->profile->occupation->id ?? null,
                'company_id'            => $user->company->id ?? null,
                'company_name'          => $user->company->name ?? null,
                'profile_image'         => $user->profile_image_url ? $user->profile_image_url : asset(config('constant.default.staff-image')),
                'user_dbs_certificate'  => $user->dbs_certificate_url ? $user->dbs_certificate_url : "",
                'user_training_doc'     => $user->training_document_url ? $user->training_document_url : "",
                'user_cv'               => $user->cv_url ? $user->cv_url : "",
                'user_staff_budge'      => $user->staff_budge_url ? $user->staff_budge_url : "",
                'user_dbs_check'        => $user->dbs_check_url ? $user->dbs_check_url : "",
                'user_training_check'   => $user->training_check_url ? $user->training_check_url : "",
                'rating'                => getStaffRating($user->id),
            ]
        ])->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Update User Profile details.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function updateProfile(Request $request){
        $request->validate([
            'name' => ['required','string','max:150'],
            'phone' => [ 'required', 'numeric', 'regex:/^[0-9]{7,15}$/', 'not_in:-', Rule::unique('users', 'phone')->whereNull('deleted_at')->ignore(auth()->user()->id, 'id')],
            'address' => ['required','string'],
            'occupation_id' => ['required', 'exists:occupations,id,deleted_at,NULL'],
            'profile_image'  =>['nullable', 'image', 'max:'.config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
        ],[
            'phone.regex' =>'The phone number length must be 7 to 15 digits.',
            'phone.unique' =>'The phone number already exists.',
            'profile_image.image' =>'Please upload image.',
            'profile_image.mimes' =>'Please upload image with extentions: jpeg,png,jpg.',
            'profile_image.max' =>'The image size must equal or less than '.config('constant.profile_max_size_in_mb'),
        ], ['occupation_id' => 'Occupation']); 

        $user = auth()->user();
        
        DB::beginTransaction();
        try {
            $user->update(['name' => $request->name,'phone' => $request->phone]);

            $user->profile()->update(['occupation_id' => $request->occupation_id, 'address' => $request->address]);

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
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.profile_updated_successfully')
            ])->setStatusCode(Response::HTTP_OK);
            
        } catch (\Exception $e) {
            DB::rollBack();
            // return $this->throwValidation([$e->getMessage()]);
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }
    
}
