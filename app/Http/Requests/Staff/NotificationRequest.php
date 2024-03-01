<?php

namespace App\Http\Requests\Staff;

use App\Rules\AtLeastOneNotNull;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMultipleSpacesRule;
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
        $staffs = $this->input('staffs');
        
        $rules['staffs']    = ['required', 'array'];
        $rules['staffs.*']  = ['exists:users,uuid'];
        $rules['section']   = ['required'];
        $rules['subject']   = ['required','string', new NoMultipleSpacesRule];
        $rules['message']   = ['required','string'];
        
        return $rules;
    }
}
