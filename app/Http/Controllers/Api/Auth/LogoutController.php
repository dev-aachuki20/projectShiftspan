<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->currentAccessToken()->delete();
            return response()->json(['status' => true, 'message' => trans('messages.logout_success')], 200);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }
}
