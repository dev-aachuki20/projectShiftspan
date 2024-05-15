<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Rules\IsActive;


class LoginController extends Controller
{

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentialsOnly = $request->validate([
            'email'    => ['required','email','exists:users,email',new IsActive],
            'password' => 'required|min:8',
        ],);


        $user = User::where('email',$request->email)->first();
        //dd($user);
        if($user){
            $remember_me = !is_null($request->remember_me) ? true : false;
            if (Auth::attempt($credentialsOnly, $remember_me))
            {   // Staff Cannot Login Into Web

                $user->current_session_id = session()->getId();
                $user->save();

                if (auth()->user()->is_staff)
                {
                    Auth::guard('web')->logout();
                    return redirect()->route('login')->withErrors(['wrongcrendials' => trans('auth.unauthorize')])->withInput($request->only('email', 'password'));
                }
                return redirect()->route('dashboard')->with('success',trans('quickadmin.qa_login_success'));
            }

            return redirect()->route('login')->withErrors(['wrongcrendials' => trans('auth.failed')])->withInput($request->only('email', 'password'));

        }else{
            return redirect()->route('login')->withErrors(['email' => trans('quickadmin.qa_invalid_email')])->withInput($request->only('email', 'password'));
        }

    }

    public function logout()
    {
        $user = Auth::user();
        $user->current_session_id = null;
        $user->save();

        Auth::guard('web')->logout();
        return redirect()->route('login');
    }
    
    public function support(){
        return view('auth.support');
    }

}
