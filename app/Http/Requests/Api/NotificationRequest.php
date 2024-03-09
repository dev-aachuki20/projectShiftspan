<?php

namespace App\Http\Requests\Api;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [];
        
        $url = url()->current();
        $lastnameofUrl = basename($url);
        
        if($lastnameofUrl == 'help-chat-store') {
            /* $notification_id = $this->input('notification_id');
            if(isset($notification_id)){
                $rules['notification_id'] = ['exists:notifications,id'];
            } */
            // $rules['subject']   = ['required','string', new NoMultipleSpacesRule];
            $rules['message']   = ['required','string'];
            $rules ['section'] = ['required', 'max:20', Rule::in(['help_chat'])];
            $rules ['section'] = ['required', 'max:20', Rule::in(['announcements', 'help_chat'])];
        }elseif($lastnameofUrl == 'announcements'){
            $rules ['section'] = ['required', 'max:20', Rule::in(['announcements'])];
        }else{
            $rules ['section'] = ['required', 'max:20', Rule::in(['help_chat'])];

        }
        
        return $rules;
    }
}
