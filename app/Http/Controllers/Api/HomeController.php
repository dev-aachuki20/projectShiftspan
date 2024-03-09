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
    
        $documents = $this->documents();  
        $settingDataArray = $settingData->toArray();
        $settingDataArray = array_merge([
            ['staff_documents' => $documents]], 
            [['settings' => $settingDataArray]
        ]);

        return $this->respondOk([
            'status'   => true,
            'message'   => trans('messages.record_retrieved_successfully'),
            'data'      => array_values($settingDataArray),
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

    /* Return User Documents */
    private function documents(){
        $user = auth()->user();
        $documents = [];
        if($user){
            $documents[] = [
                'key'           => 'relevant_training_image',
                'value'         => $user->training_document_url,
                'display_name'  => trans('cruds.staff.fields.relevant_training_image'),
            ];
        
            $documents[] = [
                'key'           => 'dbs_certificate',
                'value'         => $user->dbs_certificate_url,
                'display_name'  => trans('cruds.staff.fields.dbs_certificate'),
            ];
        
            $documents[] = [
                'key'           => 'cv',
                'value'         => $user->cv_url,
                'display_name'  => trans('cruds.staff.fields.cv'),
            ];

            $documents[] = [
                'key'           => 'staff_budge',
                'value'         => $user->staff_budge_url,
                'display_name'  => trans('cruds.staff.fields.staff_budge'),
            ];

            $documents[] = [
                'key'           => 'dbs_check',
                'value'         => $user->dbs_check_url,
                'display_name'  => trans('cruds.staff.fields.dbs_check'),
            ];

            $documents[] = [
                'key'           => 'training_check',
                'value'         => $user->training_check_url,
                'display_name'  => trans('cruds.staff.fields.training_check'),
            ];
        }

        return $documents;
    }
}

