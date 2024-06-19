<?php

use App\Models\Order;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\Uploads;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str as Str;

if (!function_exists('getCommonValidationRuleMsgs')) {
	function getCommonValidationRuleMsgs()
	{
		return [
            'currentpassword.required'=>'The current password is required.',
			'password.required' => 'The new password is required.',
			'password.min' => 'The new password must be at least 8 characters',
			'password.different' => 'The new password and current password must be different.',
			'password.confirmed' => 'The password confirmation does not match.',
			'password_confirmation.required' => 'The new password confirmation is required.',
			'password_confirmation.min' => 'The new password confirmation must be at least 8 characters',
			'email.required' => 'Please enter email address.',
			'email.email' => 'Email is not valid. Enter email address for example test@gmail.com',
            'email.exists' => "Please Enter Valid Registered Email!",
            'password_confirmation.same' => 'The confirm password and new password must match.',

			'password.regex' => 'The :attribute must be at least 8 characters and contain at least one uppercase character, one number, and one special character.',
			'password.regex' => 'The :attribute must be at least 8 characters and contain at least one uppercase character, one number, and one special character.',
		];
	}
}

if (!function_exists('generateRandomString')) {
	function generateRandomString($length = 20) {
		$randomString = Str::random($length);
		return $randomString;
	}
}

if (!function_exists('getWithDateTimezone')) {
	function getWithDateTimezone($date) {
        $newdate= Carbon::parse($date)->setTimezone(config('app.timezone'))->format('d-m-Y H:i:s');
		return $newdate;
	}
}

if (!function_exists('uploadImage')) {
	/**
	 * Upload Image.
	 *
	 * @param array $input
	 *
	 * @return array $input
	 */
	function uploadImage($directory, $file, $folder, $type="profile", $fileType="jpg",$actionType="save",$uploadId=null,$orientation=null)
	{
		$oldFile = null;
        if($actionType == "save"){
			$upload               		= new Uploads;
		}else{
			$upload               		= Uploads::find($uploadId);
			$oldFile = $upload->file_path;
		}
        $upload->file_path      	= $file->store($folder, 'public');
		$upload->extension      	= $file->getClientOriginalExtension();
		$upload->original_file_name = $file->getClientOriginalName();
		$upload->type 				= $type;
		$upload->file_type 			= $fileType;
		$upload->orientation 		= $orientation;
		$response             		= $directory->uploads()->save($upload);
        // delete old file
        if ($oldFile) {
            Storage::disk('public')->delete($oldFile);
        }

		return $upload;
	}
}

if (!function_exists('deleteFile')) {
	/**
	 * Destroy Old Image.	 *
	 * @param int $id
	 */
	function deleteFile($upload_id)
	{
		$upload = Uploads::find($upload_id);
		Storage::disk('public')->delete($upload->file_path);
		$upload->delete();
		return true;
	}
}


if (!function_exists('getSetting')) {
	function getSetting($key)
	{
		$result = null;
		$setting = Setting::where('key',$key)->where('status',1)->first();
		if ($setting->type == 'image') {
            $result = $setting->image_url;
        } elseif ($setting->type == 'file') {
            $result = $setting->doc_url;
        } elseif ($setting->type == 'json') {
            $result = $setting->value ? json_decode($setting->value, true): null;
        } else {
            $result = $setting->value;
        }
		return $result;
	}
}


if (!function_exists('str_limit_custom')) {
    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int  $limit
     * @param  string  $end
     * @return string
     */
    function str_limit_custom($value, $limit = 100, $end = '...')
    {
        return \Illuminate\Support\Str::limit($value, $limit, $end);
    }
}

if (!function_exists('getSvgIcon')) {
    function getSvgIcon($icon){
        return view('components.svg-icons', ['icon' => $icon])->render();
    }
}

if (!function_exists('getCompanyNumber')) {
	function getCompanyNumber(){
		$companyNumber = '';
		$lastEmployee = User::withTrashed()->whereHas('roles', function($query){
		   $query->where('id', config('constant.roles.sub_admin'));
		})->count();
		if($lastEmployee > 0){
			$companyNumber = config('constant.company_number_prefix').(sprintf("%06d", $lastEmployee+config('constant.company_number_start')));
		}else{
			$companyNumber = config('constant.company_number_prefix').(config('constants.company_number_start'));
		}
		return $companyNumber;
	}
}

if (!function_exists('dateFormat')) {
	function dateFormat($date, $format=''){
		$startDate = Carbon::parse($date);
		$formattedDate = $startDate->format($format);
		return $formattedDate;
	}
}

/* Send Notification to Users */
if (!function_exists('sendNotification')) {
    function sendNotification($user_id, $subject, $message, $section, $notification_type = null, $data = null)
    {
        try {
        // 	$firebaseToken = User::where('is_active', 1)->where('id', $user_id)->whereNotNull('device_token')->pluck('device_token')->all();

			$firebaseToken = User::where('id', $user_id)->whereNotNull('device_token')->pluck('device_token')->all();


			\Log::info(['firebaseToken' => $firebaseToken,'user_id'=>$user_id]);

			$response = null;
			if($firebaseToken){
				$SERVER_API_KEY = env('FIREBASE_KEY');

				\Log::info(['SERVER_API_KEY' => $SERVER_API_KEY]);

				$notification = [
					"title" => $subject,
					"body" 	=> $message,
					"sound" => "default",
					"alert" => "New"
				];

				$bodydata = [
					"title"=> $subject,
					"body" => $message,
					"group_uuid" => $data['group_uuid'] ?? null,
					"notification_id" => $data['notification_id'] ?? null,
					"data" => $data,
					"type" => $section,
				];

				$data = [
					"registration_ids"	=> $firebaseToken,
					"notification" 		=> $notification,
					"priority"			=> "high",
					"contentAvailable" 	=> true,
					"data" 				=> $bodydata
				];
				$encodedData = json_encode($data);
				$headers = [
					'Authorization: key=' . $SERVER_API_KEY,
					'Content-Type: application/json',
				];
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
				$response = curl_exec($ch);
			}
			\Log::info('Response ' . $response);
			return $response;
		} catch (\Exception $e) {
			\Log::info($e->getMessage().' '.$e->getFile().' '.$e->getCode());
		}
    }
}

if (!function_exists('calculateTimeDifferenceInMinutes')) {

	function calculateTimeDifferenceInMinutes($startTime, $endTime) {
		$start = Carbon::parse($startTime);
		$end = Carbon::parse($endTime);

		$diffInMinutes = $start->diffInMinutes($end);

		if ($diffInMinutes < 0) {
			$diffInMinutes += 1440;
		}

		return $diffInMinutes;
	}
}

if (!function_exists('getStaffRating')) {

	function getStaffRating($staff_id) {
		$user = User::where('id',$staff_id)->first();

		$totalCompletedShift = $user->assignShifts()->where('status','complete')->whereNotNull('rating')->count();

		$sumOfTotalRating = $user->assignShifts()->where('status','complete')->whereNotNull('rating')->sum('rating');

		$rating = null;

		if($totalCompletedShift > 0){
			$rating = round(((int)$sumOfTotalRating / $totalCompletedShift));
		}

		return $rating;
	}
}

if (!function_exists('checkAndChangeShiftExpireStatus')) {

	function checkAndChangeShiftExpireStatus($shift_id) {

        $currentDateTime = Carbon::now();
        $CheckShiftexpire = Shift::where('id', $shift_id)
            ->where('status', 'open')
            ->whereNull('picked_at')
            ->where(function ($query) use ($currentDateTime) {
                $query->where('end_date', '<', $currentDateTime->toDateString())
                        ->orWhere(function ($query) use ($currentDateTime) {
                            $query->where('end_date', '=', $currentDateTime->toDateString())
                                ->where('end_time', '<', $currentDateTime->toTimeString());
                        });
            })->first();

        if ($CheckShiftexpire) {
            // Shift is expired , then change status not picked
            $CheckShiftexpire->update(['status'=>'not picked']);
            return true;
        }
        // Shift is not expired
        return false;
	}
}

