<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getPolicyDoc(){
        //dd(getSetting('gdpr_policy'));
        return response()->json([
            'status'            => true,
            'message'           => trans('messages.success'),
            'data'          => [
                'privacy_policy'=> getSetting('privacy_policy') ? getSetting('privacy_policy') : "",
                'gdpr_policy'=> getSetting('gdpr_policy') ? getSetting('gdpr_policy') : "",
            ]
        ], 200);
    }
}
