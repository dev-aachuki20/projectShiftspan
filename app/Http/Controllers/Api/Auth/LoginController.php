<?php

namespace App\Http\Controllers\Api\Auth;

use Auth;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Profile;
use App\Rules\IsActive;
use App\Rules\UserHasRole;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\APIController;
use Symfony\Component\HttpFoundation\Response;


class LoginController extends APIController
{
    protected $token_type = 'Bearer';
    
    
     /**
     * Log the user in.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email','regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i','exists:users,email,deleted_at,NULL', new UserHasRole(config('constant.roles.staff'), $request->email),new IsActive],
            'password' => 'required|min:8',
        ],[
            'email.exists' => trans('validation.invalid'),
        ]);

        $clientAdmin = User::whereEmail($credentials['email'])
            ->whereHas('company', function ($query) {
                $query->where('is_active', true);
        })->first('email');
        if($clientAdmin){
            if(Auth::attempt($credentials)){
                $user = auth()->user();
                
                $user->device_token = $request->device_token;
                
                $user->last_login_at = now();
                
                $user->current_session_id = $request->header('X-Device-Id');
                
                $user->save();

                $accessToken = $user->createToken(config('auth.api_token_name'))->plainTextToken;
                
                return $this->respond([
                    'status'        => true,
                    'message'       => trans('messages.login_success'),
                    'token_type'    => $this->token_type,
                    'access_token'  => $accessToken,
                    'data'          => [
                        'id'                    => $user->id,
                        'uuid'                  => $user->uuid,
                        'name'                  => $user->name,
                        'phone'                 => $user->phone,
                        'email'                 => $user->email,
                        'address'               => $user->profile->address,
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
                    ]
                ]);
                
            }else{ 
                return $this->throwValidation(trans('messages.wrong_credentials'));
            }
        }else{
            return $this->setStatusCode(401)->respondWithError(trans('messages.staff_account_deactivate'));
        }
        
    }


    public function forgotPassword(Request $request)
    {
        $validator = $request->validate(['email' => ['required','email','exists:users,email,deleted_at,NULL', new UserHasRole(config('constant.roles.staff'), $request->email), new IsActive]]);

        DB::beginTransaction();
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $token = rand(100000, 999999);

            DB::table('password_resets')
            ->updateOrInsert(
                ['email' => $request->email,'token' => $token,'created_at' => Carbon::now()],
                ['email' => $request->email]
            );

            $subject = "Reset Password OTP";
            $expiretime = '2 Minutes';
            $user->sendPasswordResetOtpNotification($user,$token, $subject , $expiretime);
            DB::commit();

            return $this->respondOk([
                'status' => true,
                'message' => trans('auth.messages.forgot_password.otp_sent'),
            ])->setStatusCode(Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            return $this->throwValidation([trans('messages.error_message')]);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validation =  $request->validate([
            'email' => 'required|email|exists:password_resets,email',
            'otp'   => 'required|numeric|min:8'
        ]);

        $passwordReset = DB::table('password_resets')
        ->where('token', $request->otp)
        ->where('email', $request->email)
        ->latest()
        ->first();

        if (!$passwordReset) {
            return $this->throwValidation(trans('auth.messages.forgot_password.validation.invalid_otp'));
        }

        if (Carbon::parse($passwordReset->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return $this->throwValidation(trans('auth.messages.forgot_password.validation.expire_otp'));
        }

        return $this->respondOk([
            'success' => true,
            'token' => encrypt($request->otp),
            'message' => trans('auth.messages.forgot_password.validation.verified_otp'),
        ]);

    }

    public function resetPassword(Request $request)
    {
        $validation = $request->validate([
            'token'     => 'required',
            'email'     => 'required|email|exists:users,email,deleted_at,NULL',
            'password'  => 'required|string|min:8',
            'confirmed_password' => 'required|string|same:password',
        ]);

        $token = decrypt($request->token);
        $passwordReset = DB::table('password_resets')->where('token',$token)
                ->where('email', $request->email)
                ->orderBy('created_at','desc')
                ->first();

        if (!$passwordReset){
            return $this->throwValidation(trans('auth.messages.forgot_password.validation.invalid_token_email'));
        }

        $expireTime = config('auth.passwords.users.expire');
        if (Carbon::parse($passwordReset->created_at)->addMinutes($expireTime)->isPast()) {
            DB::table('password_resets')->where('email',$passwordReset->email)->delete();
            return $this->throwValidation(trans('auth.messages.forgot_password.validation.expire_otp'));            
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user){
            return $this->throwValidation(trans('auth.messages.forgot_password.validation.email_not_found'));
        }
            
        $user->password = bcrypt($request->password);
        $user->save();
        DB::table('password_resets')->where('email',$passwordReset->email)->delete();

        return $this->respondOk([
            'success' => true,
            'message' => trans('auth.messages.forgot_password.success_update'),
        ]);     
    }

}
