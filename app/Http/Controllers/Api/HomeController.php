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
        /*$settingData = [
            'privacy_policy'=> getSetting('privacy_policy') ? getSetting('privacy_policy') : "",
            'gdpr_policy'=> getSetting('gdpr_policy') ? getSetting('gdpr_policy') : "",
        ];*/
        
        $setting = Setting::whereIn('key',['privacy_policy','gdpr_policy'])->where('status',1)->get();
        dd($setting);
        $settingData = $setting->map(function ($input) {
        	if ($input->type == 'image') {
                $keyValue = $input->image_url;
            } elseif ($input->type == 'file') {
                $keyValue = $input->doc_url;
            } else {
                $keyValue = $input->value;
            }
			return [
				'id' => $input->id,
				'key' => $input->user->name,
				'value' 	=> $input->message,
			];

		});
		if ($setting->type == 'image') {
            $result = $setting->image_url;
        } elseif ($setting->type == 'file') {
            $result = $setting->doc_url;
        } else {
            $result = $setting->value;
        }
		return $result;
        
        return $this->respondOk([
            'status'   => true,
            'message'   => trans('messages.record_retrieved_successfully'),
            'data'      => $companyList,
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
        $occupationList = auth()->user()->company->occupations()->select('id','name')->orderBy('name', 'asc')->get()->map(function($row){
            return [
                'id' => $row->id,
                'name' => $row->name,
            ];
        });
        return $this->respondOk([
            'status'   => true,
            'message'   => trans('messages.record_retrieved_successfully'),
            'data'      => $occupationList,
        ])->setStatusCode(Response::HTTP_OK);
    }
}
