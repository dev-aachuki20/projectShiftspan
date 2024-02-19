<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $settings = Setting::whereStatus(1)->whereGroup('web')->orderBy('position', 'asc')->get();
        return view('admin.setting.index',compact('settings'));
    }

    public function update(UpdateRequest $request, Setting $setting)
    {
        $data=$request->all();
        try {
            DB::beginTransaction();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                $setting_value = $value;
                if ($setting) {
                    if ($setting->type === 'image') {                        
                        if ($value) {
                            $uploadId = $setting->image ? $setting->image->id : null;
                            if($uploadId){
                                uploadImage($setting, $value, 'settings/images/',"setting-image", 'original', 'update', $uploadId);
                            }else{
                                uploadImage($setting, $value, 'settings/images/',"setting-image", 'original', 'save', null);
                            }
                        } 
                        $setting_value = null;
                    }
                    elseif($setting->type === 'file'){
                        if ($value) {
                            $uploadId = $setting->image ? $setting->image->id : null;
                            if($uploadId){
                                uploadImage($setting, $value, 'settings/doc/',"setting-file", 'original', 'update', $uploadId);
                            }else{
                                uploadImage($setting, $value, 'settings/doc/',"setting-file", 'original', 'save', null);
                            }
                        } 
                        $setting_value = null;
                    } else {
                        // Handle other fields
                        $setting->value = $setting_value;
                    }
                    $setting->save();
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.update_record'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }

    public function showContactDetails()
    {
        abort_if(Gate::denies('setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $settings = Setting::whereStatus(1)->whereGroup('support')->orderBy('position', 'asc')->get();
        return view('admin.setting.contact-detail',compact('settings'));
    }

    public function updateContactDetails(Request $request, Setting $setting)
    {
        $request->validate([
            'support_email'  => ['required', 'email'],
            'support_phone' => [
                'required',
                'integer',
                'regex:/^[0-9]{7,15}$/',
                'not_in:-'
            ],
        ],[
            'support_phone.required'=>'The phone number field is required',
            'support_phone.regex' =>'The phone number length must be 7 to 15 digits.',
            'support_phone.unique' =>'The phone number already exists.',
        ]);
        $data=$request->all();
        try {
            DB::beginTransaction();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                $setting_value = $value;
                if ($setting) {                    
                    $setting->value = $setting_value;
                    $setting->save();
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.update_record'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }
}
