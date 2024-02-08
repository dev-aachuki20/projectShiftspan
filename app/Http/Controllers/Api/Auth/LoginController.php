<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Rules\IsActive;
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
            'email'    => ['required','email'],
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
            // dd($e->getMessage().'->'.$e->getLine());
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

}
