<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Rules\IsActive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email'    => ['required','email','exists:users',new IsActive],
            'password' => 'required|min:8',
        ]);

        if($validator->fails()){
            //Error Response Send
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user->is_approved) {
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.not_approved'),
                ];
            return response()->json($responseData, 400);
        }

        DB::beginTransaction();
        try {
            $remember_me = !is_null($request->remember) ? true : false;
            $credentialsOnly = [
                'email'    => $request->email,
                'password' => $request->password,
            ];

            if(Auth::attempt($credentialsOnly, $remember_me)){
                $user = auth()->user();
                $accessToken = $user->createToken(config('auth.api_token_name'))->plainTextToken;
                DB::commit();
                $responseData = [
                    'status'            => true,
                    'message'           => trans('messages.login_success'),
                    'userData'          => [
                        'id'           => $user->id,
                        'name'   => $user->name ?? '',
                        'username'    => $user->username ?? '',
                        'email'    => $user->email ?? '',
                        'profile_image'=> $user->profile_image_url ?? '',
                    ],
                    'remember_me_token' => $user->remember_token,
                    'access_token'      => $accessToken
                ];
                return response()->json($responseData, 200);
            } else{
                $responseData = [
                    'status'        => false,
                    'error'         => trans('messages.wrong_credentials'),
                ];
                return response()->json($responseData, 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //dd($e->getMessage().'->'.$e->getLine());
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function registerUser(Request $request){
        $role_id= config('app.roleid.staff');
        $validator = Validator::make($request->all(), [
            'name' => ['required','string'],
            // 'email'    => ['required','email:dns','unique:users,email,NULL,id,deleted_at,NULL'],
            'email'    => ['required','email:dns','unique:users,email,NULL,id'],
            'password'   => ['required', 'string', 'min:8','confirmed'],
            'password_confirmation' => ['required','min:8','same:password'],
            'is_criminal' => ['required','boolean'],
            'sub_admin_id'=> ['nullable','numeric'],
            'user_dbs_certificate' => ['required','file','max:2048','mimes:jpeg,png,pdf,doc,docx'],
            'user_cv' => ['required','file','max:2048','mimes:jpeg,png,pdf,doc,docx'],
            'other_doc' => ['required','file','max:2048','mimes:jpeg,png,pdf,doc,docx'],
        ]);

        if($validator->fails()){
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 400);
        }

        $data= [
            'name'=>$request->name,
            'email'=>$request->email,
            'username'=>$request->email,
            'sub_admin_id'=>$request->sub_admin_id,
            'email_verified_at'=> now(),
            'password'=>Hash::make($request->password),
        ];

        DB::beginTransaction();
        try {
            $user=User::create($data);
            $user->update(['created_by' => $user->id]);
            $profile= Profile::create([
                'user_id' => $user->id,
                'is_criminal' => $request->is_criminal,
            ]);
            $user->roles()->sync($role_id);
            $request->hasFile('user_dbs_certificate') ? uploadImage($user, $request->file('user_dbs_certificate'), 'user/dbs-doc', "user_dbs_certificate", 'original') : null;
            $request->hasFile('user_cv') ? uploadImage($user, $request->file('user_cv'), 'user/cv-doc', "user_cv", 'original') : null;
            $request->hasFile('other_doc') ? uploadImage($user, $request->file('user_dbs_certificate'), 'user/other_doc', "user_dbs_certificate", 'original') : null;

            DB::commit();
            $responseData = [
                'status'            => true,
                'message'           => trans('messages.register_success'),
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            DB::rollBack();
             dd($e->getMessage().'->'.$e->getLine());
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(), ['email' => ['required','email','exists:users',new IsActive]]);

        if($validator->fails()){
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 401);
        }

        DB::beginTransaction();
        try {
            $token = rand(100000, 999999);
            $email_id = $request->email;
            $user = User::where('email', $email_id)->first();
            if(!$user){
                $responseData = [
                    'status'        => false,
                    'error'         => trans('messages.invalid_email'),
                ];
                return response()->json($responseData, 401);
            }

            DB::table('password_resets')->insert([
                'email'         => $email_id,
                'token'         => $token,
                'created_at'    => Carbon::now()
            ]);

            $user->otp = $token;
            $user->subject = "Reset Password OTP";
            $user->expiretime = '2 Minutes';
            $user->sendPasswordResetOtpNotification($request, $user);
            DB::commit();
            //Success Response Send
            $responseData = [
                'status'        => true,
                'otp_time_allow' => config('auth.passwords.users.expire').' Minutes',
                'otp' => $token,
                'message'         => trans('messages.otp_sent_email'),
            ];
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            //dd($e->getMessage().'->'.$e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|exists:password_resets,email',
            'otp'   => 'required|numeric|min:6'
        ]);
        if ($validation->fails()) {
            $responseData = [
                'status'        => false,
                'validation_errors' => $validation->errors(),
            ];
            return response()->json($responseData, 401);
        }
        $email = $request->email;
        $otpToken = $request->otp;

        $passwordReset = DB::table('password_resets')->where('token', $otpToken)
                ->where('email', $email)
                ->orderBy('created_at','desc')
                ->first();

        if (!$passwordReset){
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.invalid_otp'),
            ];
            return response()->json($responseData, 401);
        }

        if (Carbon::parse($passwordReset->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.expire_otp'),
            ];
            return response()->json($responseData, 401);
        }

        $responseData = [
            'status'        => true,
            'token'         => encrypt($otpToken),
            'message'         => trans('messages.verified_otp'),
        ];
        return response()->json($responseData, 200);
    }

    public function resetPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required',
            'email'     => 'required|email|exists:password_resets,email',
            'password'  => 'required|string|min:8',
            'confirmed_password' => 'required|string|min:8',
        ]);

        if ($validation->fails()) {
            $responseData = [
                'status'        => false,
                'validation_errors' => $validation->errors(),
            ];
            return response()->json($responseData, 401);
        }
        $token = decrypt($request->token);
        $passwordReset = DB::table('password_resets')->where('token',$token)
                ->where('email', $request->email)
                ->orderBy('created_at','desc')
                ->first();

        if (!$passwordReset)
        {
            $responseData = [
                'status'        => false,
                'validation_errors' => trans('messages.invalid_token_email'),
            ];
            return response()->json($responseData, 401);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user){
            $responseData = [
                'status'        => false,
                'validation_errors' => trans('messages.invalid_email'),
            ];
            return response()->json($responseData, 401);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        DB::table('password_resets')->where('email',$passwordReset->email)->delete();
        $responseData = [
            'status'        => true,
            'message'         => trans('passwords.reset'),
        ];
        return response()->json($responseData, 200);
    }

}
