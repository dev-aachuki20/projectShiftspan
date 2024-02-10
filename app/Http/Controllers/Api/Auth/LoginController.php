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
    public function login(Request $request)
    {
        $credentialsOnly = $request->validate([
            'email'    => ['required','email','exists:users,email',new IsActive],
            'password' => 'required|min:8',
        ]);

        if(Auth::attempt($credentialsOnly)){
            $user = auth()->user();
            $accessToken = $user->createToken(config('auth.api_token_name'))->plainTextToken;
            return response()->json([
                'status'            => true,
                'message'           => trans('messages.login_success'),
                'userData'          => [
                    'id'           => $user->id,
                    'name'   => $user->name ?? null,
                    'phone'   => $user->phone ?? null,
                    'email'    => $user->email ?? null,
                    'address'   => $user->profile->address ?? null,
                    'occupation_name'    => $user->profile->occupation->name ?? null,
                    'company_name'    => $user->company->name ?? null,
                    'profile_image'=> $user->profile_image_url ? $user->profile_image_url : asset(config('app.default.staff-image')),
                    'user_dbs_certificate'=> $user->dbs_certificate_url ? $user->dbs_certificate_url : "",
                    'user_training_doc'=> $user->training_document_url ? $user->training_document_url : "",
                    'user_cv'=> $user->cv_url ? $user->cv_url : "",
                    'user_staff_budge'=> $user->staff_budge_url ? $user->staff_budge_url : "",
                    'user_dbs_check'=> $user->dbs_check_url ? $user->dbs_check_url : "",
                    'user_training_check'=> $user->training_check_url ? $user->training_check_url : "",
                ],
                'access_token'      => $accessToken
            ], 200);
        } else{
            return response()->json([
                'status'        => false,
                'error'         => trans('messages.wrong_credentials'),
            ], 401);
        }
    }

    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => ['required','string'],
            'email'    => ['required','email:dns','unique:users,email,NULL,id'],
            'password'   => ['required', 'string', 'min:8','confirmed'],
            'password_confirmation' => ['required','min:8','same:password'],
            'is_criminal' => ['required','boolean'],
            'sub_admin_id'=> ['nullable','numeric'],
            'occupation_id'=> ['nullable','numeric','exists:occupations,id'],
            'user_dbs_certificate' => ['required','file','max:2048','mimes:pdf'],
            'user_cv' => ['required','file','max:2048','mimes:pdf'],
            'user_training_doc' => ['required','file','max:2048','mimes:pdf'],
            'user_staff_budge' => ['required','file','max:2048','mimes:pdf'],
            'user_dbs_check' => ['required','file','max:2048','mimes:pdf'],
            'user_training_check' => ['nullable','file','max:2048','mimes:pdf'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'sub_admin_id' => $request->sub_admin_id,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'is_active' => 0
            ]);
            $user->profile()->create([
                'occupation_id' => $request->occupation_id,
                'is_criminal' => $request->is_criminal,
            ]);

            $user->roles()->sync(config('app.roleid.staff'));
            foreach ($request->allFiles() as $key => $file) {
                if (in_array($key,config('constant.staff_file_fields'))) {
                    uploadImage($user, $file, 'staff/doc', $key, 'original');
                }
            }

            DB::commit();
            return response()->json([
                'status'            => true,
                'message'           => trans('messages.register_success'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            return response()->json([
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = $request->validate(['email' => ['required','email','exists:users,email',new IsActive]]);

        DB::beginTransaction();
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $token = rand(100000, 999999);

            $passwordReset = DB::table('password_resets')->insert([
                'email'      => $request->email,
                'token'      => $token,
                'created_at' => now()
            ]);

            $subject = "Reset Password OTP";
            $expiretime = '2 Minutes';
            $user->sendPasswordResetOtpNotification($user,$token, $subject , $expiretime);
            DB::commit();
            return response()->json([
                'status'        => true,
                'otp_time_allow' => config('auth.passwords.users.expire').' Minutes',
                'otp' => $token,
                'message'         => trans('messages.otp_sent_email'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return response()->json([
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ], 500);
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
            return response()->json(['status' => false, 'error' => trans('messages.invalid_otp')], 401);
        }

        if (Carbon::parse($passwordReset->created_at)
            ->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return response()->json(['status' => false, 'error' => trans('messages.expire_otp')], 401);
        }

        return response()->json([
            'status' => true,
            'token' => encrypt($request->otp),
            'message' => trans('messages.verified_otp'),
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validation = $request->validate([
            'token' => 'required',
            'email'     => 'required|email|exists:users,email',
            'password'  => 'required|string|min:8',
            'confirmed_password' => 'required|string|min:8',
        ]);

        $token = decrypt($request->token);
        $passwordReset = DB::table('password_resets')->where('token',$token)
                ->where('email', $request->email)
                ->orderBy('created_at','desc')
                ->first();

        if (!$passwordReset)
        {
            return response()->json([
                'status' => false,
                'validation_errors' => trans('messages.invalid_token_email'),
            ], 403);
        }

        if (!$user = User::where('email', $passwordReset->email)->first()) {
            return response()->json(['status' => false, 'validation_errors' => trans('messages.invalid_email')], 401);
        }

        $user->update(['password' => bcrypt($request->password)]);
        DB::table('password_resets')->where('email',$passwordReset->email)->delete();

        return response()->json([
            'status' => true,
            'message' => trans('passwords.reset'),
        ], 200);
    }

}
