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
        $settings = Setting::whereStatus(1)->whereIn('group', ['web', 'api'])->orderBy('id', 'asc')->orderBy('position', 'asc')->get();
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
                            $uploadId = $setting->doc ? $setting->doc->id : null;
                            if($uploadId){
                                uploadImage($setting, $value, 'settings/doc/', "setting-file", 'original', 'update', $uploadId);
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
        // abort_if(Gate::denies('setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $settings = Setting::whereStatus(1)->whereGroup('support')->orderBy('position', 'asc')->get();
        return view('admin.setting.contact-detail',compact('settings'));
    }

    public function updateContactDetails(Request $request, Setting $setting)
    {
        $request->validate([
            'support_email'  => ['required', 'email'],
            'support_phone' => [
                'required',
                'string',
                'regex:/^(?:[+\-\d]+(?:\s[+\-\d]+)*)$/',
                //'numeric',
                // 'regex:/^[0-9]{7,15}$/',
                //'not_in:-'
            ],
        ],[
            'support_phone.required'=>'The phone number field is required',
            'support_phone.regex' =>'The phone number may only contain numbers, a single space, plus and minus signs.',
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

    public function storeSubject(Request $request)
    {
        abort_if(Gate::denies('setting_message_subject_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'subject_name'  => ['required',  'string', 'max:255']
        ]);

        $setting = Setting::where('key', 'message_subject')->first();
        $currentArray = $setting->value ? json_decode($setting->value, true) : [];
        $newValue = $request->subject_name;
        $currentArray[] = $newValue;

        $setting->update(['value' => json_encode($currentArray)]);
        return response()->json([
            'success' => true,
            'message' => trans('messages.crud.add_record'),
        ], 200);
    }

    public function deleteSubject(Request $request)
    {
        abort_if(Gate::denies('setting_message_subject_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $value = decrypt($request->data);
        $setting = Setting::where('key', 'message_subject')->first();
        $currentArray = $setting->value ? json_decode($setting->value, true) : null;
        if (in_array($value, $currentArray)) {
            $key = array_search($value, $currentArray);
            array_splice($currentArray, $key, 1);
            $setting->update(['value' => json_encode($currentArray)]);
            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.delete_record'),
            ], 200);
        } else {
            return response()->json(['success' => false, 'message' => trans('messages.error_message')],505);
        }
    }
}
