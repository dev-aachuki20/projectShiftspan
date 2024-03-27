<?php

namespace App\Http\Controllers\Api\Auth;

use DB;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\APIController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;

class RegisterController extends APIController
{
    
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'title'             => ['required','string','max:10'],
            'name'              => ['required','string','max:150'],
            'email'             => ['required','email','regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i','unique:users,email,NULL,id,deleted_at,NULL'],
            'password'          => ['required', 'string', 'min:8','confirmed'],
            'company_id'        => ['required','numeric','exists:users,id,deleted_at,NULL','exists:role_user,user_id,role_id,'.config('constant.roles.sub_admin')],
            'occupation_id'     => ['required', 'exists:occupations,id,deleted_at,NULL'],
            'is_criminal'       => ['required','boolean','in:1,0'],
            'user_dbs_certificate' => ['required','file','max:2048','mimes:pdf'],
            'user_cv'           => ['required','file','max:2048','mimes:pdf'],
            'user_training_doc' => ['required','file','max:2048','mimes:pdf'],
            'user_staff_budge'  => ['required','file','max:2048','mimes:pdf'],
            'user_dbs_check'    => ['required','file','max:2048','mimes:pdf'],
            'user_training_check' => ['nullable','file','max:2048','mimes:pdf'],
        ],[],['occupation_id' => 'Occupation']);
        
        DB::beginTransaction();
        try {
            $user = User::create([
                'title' => ucfirst($request->title),
                'name' => $request->name,
                'email' => $request->email,
                'company_id' => $request->company_id,
                'password' => Hash::make($request->password),
                'is_active' => 0,
            ]);

            $user->profile()->create([
                'is_criminal' => $request->is_criminal,
                'occupation_id' => $request->occupation_id,
            ]);

            $user->roles()->sync(config('constant.roles.staff'));
            foreach ($request->allFiles() as $key => $file) {
                if (in_array($key,config('constant.staff_file_fields'))) {
                    uploadImage($user, $file, 'staff/doc', $key, 'original');
                }
            }

            DB::commit();
            
            $key = array_search(config('constant.notification_subject.announcements'), config('constant.notification_subject'));
            $messageData = [
                'notification_type' => array_search(config('constant.subject_notification_type.registration_completion_deactive'), config('constant.subject_notification_type')),
                'section'           => $key,
                'subject'           => trans('messages.registration_completion_admin_subject'),
                'message'           => trans('messages.registration_completion_admin_message', [
                    'username'      => $request->name,
                ]),
            ];
            
            Notification::send($user, new SendNotification($messageData));

            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.register_success')
            ])->setStatusCode(Response::HTTP_OK);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info($e->getMessage().' '.$e->getFile().' '.$e->getLine());
            // return $this->throwValidation([$e->getMessage()]);
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }


}
