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
        $settings = Setting::whereStatus(1)->whereGroup('web')->get();
        return view('admin.setting.index',compact('settings'));
    }

    public function update(UpdateRequest $request, Setting $setting)
    {
        $data=$request->all();
        dd($data);
        try {
            DB::beginTransaction();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                $setting_value = $value;
                if ($setting) {
                    if ($setting->type === 'image') {
                        $uploadId = $setting->image ? $setting->image->id : null;
                        if ($value) {
                            if($uploadId){
                                uploadImage($setting, $value, 'settings/images/',"setting", 'original', 'update', $uploadId);
                            }else{
                                uploadImage($setting, $value, 'settings/images/',"setting", 'original', 'save', null);
                            }
                        }else{
                            if($uploadId){
                                deleteFile($uploadId);
                            }
                        }

                        $setting_value = null;

                    }
                    elseif($setting->type === 'file'){

                        /// code pending     path 'settings/doc/'

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
                'alert-type'=> trans('quickadmin.alert-type.success')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }


}
