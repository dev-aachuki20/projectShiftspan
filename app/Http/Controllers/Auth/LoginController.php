<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Rules\IsActive;
use Illuminate\Validation\ValidationException;


class LoginController extends Controller
{

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        dd('Working on');
        $field = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $emailValidation = ['required'];

        $validated = $request->validate([
            $field    => ['required','email',new IsActive],
            'password' => 'required|min:8',
        ],[
            'email.required' => 'The Email is required.',
            'password.required' => 'The Password is required.',
        ]);

        $remember_me = !is_null($request->remember_me) ? true : false;
        $credentialsOnly = [
            'email'    => $request->email,
            'password' => $request->password,
        ];
        try {
            $user = User::where('email',$request->email)->first();
            if($user){
                if (Auth::attempt($credentialsOnly, $remember_me)) {
                    // Staff Cannot Login Into Web
                   // dd(auth()->user()->getRoleNames());
                    if ((auth()->user()->hasRole(config('app.roleid.staff')))) {
                        Auth::guard('web')->logout();
                        return redirect()->route('login')->withErrors(['wrongcrendials' => trans('auth.unauthorize')])->withInput($request->only('email', 'password'));
                    }
                    //return redirect()->route('dashboard')->with('success',trans('quickadmin.qa_login_success'));
                    return redirect()->route('dashboard')->with(['success' => true,
                    'message' => trans('quickadmin.qa_login_success'),
                    'title'=> trans('quickadmin.qa_login'),
                    'alert-type'=> trans('quickadmin.alert-type.success')]);
                }

                return redirect()->route('login')->withErrors(['wrongcrendials' => trans('auth.failed')])->withInput($request->only('email', 'password'));

            }else{
                return redirect()->route('login')->withErrors(['email' => trans('quickadmin.qa_invalid_email')])->withInput($request->only('email'));
            }

        } catch (ValidationException $e) {
            return redirect()->route('login')->withErrors($validated)->withInput($request->only('email', 'password'));
        }

    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }

}
