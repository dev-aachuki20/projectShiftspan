<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\APIController;
use App\Models\User;
use App\Models\Setting;
use App\Models\Occupation;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends APIController
{
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke all tokens...
        $request->user()->tokens()->delete();
        // Revoke the token that was used to authenticate the current request...
        // $request->user()->currentAccessToken()->delete();
        return $this->respondOk([
            'success'   => true,
            'message'   => trans('auth.messages.logout.success'),
        ]);
    }


    /**
     * Setting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setting(Request $request)
    {
        $setting = Setting::where('group','api')->where('status',1)->get();
        // $setting = Setting::whereIn('key',['privacy_policy','gdpr_policy'])->where('status',1)->get();
        $settingData = $setting->map(function ($input) {
        	if ($input->type == 'image') {
                $keyValue = $input->image_url;
            } elseif ($input->type == 'file') {
                $keyValue = $input->doc_url;
            } else {
                $keyValue = $input->value;
            }
			return [
				'key'       => $input->key,
				'value' 	=> $keyValue,
				'display_name' 	=> $input->display_name,
			];

		});

        return $this->respondOk([
            'status'   => true,
            'message'   => trans('messages.record_retrieved_successfully'),
            'data'      => $settingData,
        ])->setStatusCode(Response::HTTP_OK);
    }


    public function companyList(){
        $company_role_id= config('constant.roles.sub_admin');
        $companyList = User::select('id','name')->whereHas('roles', function ($query) use ($company_role_id) {
            $query->where('id', $company_role_id);
        })->orderBy('name', 'asc')->get();

        return $this->respondOk([
            'status'   => true,
            'message'   => trans('messages.record_retrieved_successfully'),
            'data'      => $companyList,
        ])->setStatusCode(Response::HTTP_OK);
    }


    public function occupationsList(){
        $occupationList = auth()->user()->company->occupations()->select('id','name')->orderBy('name', 'asc')->get()->makeHidden('pivot');
        return $this->respondOk([
            'status'   => true,
            'message'   => trans('messages.record_retrieved_successfully'),
            'data'      => $occupationList,
        ])->setStatusCode(Response::HTTP_OK);
    }

    public function locationsList(){
        $occupationList = auth()->user()->company->locations()->select('id','name')->orderBy('name', 'asc')->get()->makeHidden('pivot');
        return $this->respondOk([
            'status'   => true,
            'message'   => trans('messages.record_retrieved_successfully'),
            'data'      => $occupationList,
        ])->setStatusCode(Response::HTTP_OK);
    }
}

