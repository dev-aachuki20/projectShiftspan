<?php

namespace App\Http\Controllers\Api\Auth;

use DB;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\APIController;
use Symfony\Component\HttpFoundation\Response;


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
            'name'              => ['required','string','max:150'],
            'email'             => ['required','email:dns','unique:users,email,NULL,id,deleted_at,NULL'],
            'password'          => ['required', 'string', 'min:8','confirmed'],
            'company_id'        => ['required','numeric','exists:users,id,deleted_at,NULL','exists:role_user,role_id,user_id,'.config('constant.roles.sub_admin')],
            
            'is_criminal'       => ['required','boolean','in:1,0'],
            'user_dbs_certificate' => ['required','file','max:2048','mimes:pdf'],
            'user_cv'           => ['required','file','max:2048','mimes:pdf'],
            'user_training_doc' => ['required','file','max:2048','mimes:pdf'],
            'user_staff_budge'  => ['required','file','max:2048','mimes:pdf'],
            'user_dbs_check'    => ['required','file','max:2048','mimes:pdf'],
            'user_training_check' => ['nullable','file','max:2048','mimes:pdf'],
        ]);
        
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'company_id' => $request->company_id,
                'password' => Hash::make($request->password),
                'is_active' => 0,
            ]);

            $user->profile()->create([
                'is_criminal' => $request->is_criminal,
            ]);

            $user->roles()->sync(config('constant.roles.staff'));
            foreach ($request->allFiles() as $key => $file) {
                if (in_array($key,config('constant.staff_file_fields'))) {
                    uploadImage($user, $file, 'staff/doc', $key, 'original');
                }
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.register_success')
            ])->setStatusCode(Response::HTTP_OK);
            
        } catch (\Exception $e) {
            DB::rollBack();
            // return $this->throwValidation([$e->getMessage()]);
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }


}
