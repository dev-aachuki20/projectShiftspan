<?php

namespace App\Http\Requests\Staff;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
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
        $method = $this->input('_method');
        $ids = $this->input('ids');
        if (!empty($ids)) {
            /* For Delete Multiple Data */
            $rules['ids'] = ['required', 'array'];
            $rules['ids.*'] = ['exists:users,uuid'];
        }else{
            if(!empty($method == 'PUT')){
                $rules['title']                 = ['required', 'regex:/^[a-zA-Z\s]+$/','string', 'max:10', new NoMultipleSpacesRule];
                $rules['phone']                 = ['required', 'numeric', 'regex:/^[0-9]{7,15}$/','unique:users,phone,'. $this->staff.',uuid,deleted_at,NULL'];
                $rules['dob']                   = ['required', 'date', 'before_or_equal:' . now()->format('Y-m-d')];
            }else{
                $rules['company_id']            = ['nullable'];
                /* $rules['name']                  = ['required', 'regex:/^[a-zA-Z\s]+$/','string', 'max:255', new NoMultipleSpacesRule];
                $rules['username']              = ['required', 'alpha_num', 'string', 'regex:/^\S*$/', Rule::unique('users')->ignore($this->input('id'), 'uuid')->whereNull('deleted_at')]; */

                $rules['title']                 = ['required', 'regex:/^[a-zA-Z\s]+$/','string', 'max:10', new NoMultipleSpacesRule];
                $rules['name']                  = ['required', 'regex:/^[a-zA-Z\s]+$/','string', 'max:255', new NoMultipleSpacesRule];
                $rules['email']                 = ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i', Rule::unique('users', 'email')->ignore($this->input('id'), 'uuid')->whereNull('deleted_at')];
                $rules['phone']                 = ['required', 'numeric', 'regex:/^[0-9]{7,15}$/', Rule::unique('users', 'phone')->ignore($this->input('id'), 'uuid')->whereNull('deleted_at')];
                $rules['password']              = ['required', 'string', 'min:8', 'max:15', /* 'regex:/^(?!.*\s)(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/' */];
                $rules['dob']                   = ['required', 'date', 'before_or_equal:' . now()->format('Y-m-d')];
            }
    
            $rules['previous_name']             = ['nullable', 'string'];
            $rules['national_insurance_number'] = ['nullable', 'string'];
            $rules['address']                   = ['nullable', 'string'];
            $rules['education']                 = ['nullable', 'string'];
            $rules['prev_emp_1']                = ['nullable', 'string'];
            $rules['prev_emp_2']                = ['nullable', 'string'];
            $rules['reference_1']               = ['nullable', 'string'];
            $rules['reference_2']               = ['nullable', 'string'];
            $rules['date_sign']                 = ['required', 'date','before_or_equal:' . now()->format('Y-m-d')];
            $rules['is_criminal']               = ['required','boolean'];
            $rules['is_rehabilite']             = ['required','boolean'];
            $rules['is_enquire']                = ['required','boolean'];
            $rules['is_health_issue']           = ['required','boolean'];
            $rules['is_statement']              = ['required','boolean'];
            
            $rules['image']                     = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'];
            $rules['relevant_training']         = ['nullable', 'file', 'mimes:pdf', 'max:2048'];
            $rules['dbs_certificate']           = ['nullable', 'file', 'mimes:pdf', 'max:2048'];
            $rules['cv_image']                  = ['nullable', 'file', 'mimes:pdf', 'max:2048'];
            $rules['staff_budge']               = ['nullable', 'file', 'mimes:pdf', 'max:2048'];
            $rules['dbs_check']                 = ['nullable', 'file', 'mimes:pdf', 'max:2048'];
            $rules['training_check']            = ['nullable', 'file', 'mimes:pdf', 'max:2048'];

        }
        

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('validation.required', ['attribute' => strtolower(__('cruds.user.fields.name'))]),
            'name.regex' => __('validation.regex', ['attribute' => strtolower(__('cruds.user.fields.name'))]),
            'name.string' => __('validation.string', ['attribute' => strtolower(__('cruds.user.fields.name'))]),
            'name.max' => __('validation.max.string', ['attribute' => strtolower(__('cruds.user.fields.name')), 'max' => ':max']),

            'password.required' => __('validation.required', ['attribute' => strtolower(__('cruds.user.fields.password'))]),
            'password.string' => __('validation.string', ['attribute' => strtolower(__('cruds.user.fields.password'))]),
            'password.min' => __('validation.min.string', ['attribute' => strtolower(__('cruds.user.fields.password')), 'min' => ':min']),
            'password.max' => __('validation.max.string', ['attribute' => strtolower(__('cruds.user.fields.password')), 'max' => ':max']),
            // 'password.regex' => __('validation.password.regex', ['attribute' => strtolower(__('cruds.user.fields.password'))]),

            'email.required' => __('validation.required', ['attribute' => strtolower(__('cruds.user.fields.email'))]),
            'email.ends_with' => __('validation.ends_with', ['attribute' => strtolower(__('cruds.user.fields.email'))]),
            'email.unique' => __('validation.unique', ['attribute' => strtolower(__('cruds.user.fields.email'))]),

            'username.required' => __('validation.required', ['attribute' => strtolower(__('cruds.user.fields.username'))]),
            'username.string' => __('validation.string', ['attribute' => strtolower(__('cruds.user.fields.username'))]),
            'username.unique' => __('validation.unique', ['attribute' => strtolower(__('cruds.user.fields.username'))]),

            'is_criminal.required' => __('validation.required', ['attribute' => strtolower(__('cruds.staff.fields.criminal'))]),
            'is_criminal.boolean' => __('validation.boolean', ['attribute' => strtolower(__('cruds.staff.fields.criminal'))]),
            
            'is_rehabilite.required' => __('validation.required', ['attribute' => strtolower(__('cruds.staff.fields.rehabilitation_of_offenders'))]),
            'is_rehabilite.boolean' => __('validation.boolean', ['attribute' => strtolower(__('cruds.staff.fields.rehabilitation_of_offenders'))]),
            
            'is_enquire.required' => __('validation.required', ['attribute' => strtolower(__('cruds.staff.fields.enquires'))]),
            'is_enquire.boolean' => __('validation.boolean', ['attribute' => strtolower(__('cruds.staff.fields.enquires'))]),
            
            'is_health_issue.required' => __('validation.required', ['attribute' => strtolower(__('cruds.staff.fields.health_issue'))]),
            'is_health_issue.boolean' => __('validation.boolean', ['attribute' => strtolower(__('cruds.staff.fields.health_issue'))]),

            'is_statement.required' => __('validation.required', ['attribute' => strtolower(__('cruds.staff.fields.statement'))]),
            'is_statement.boolean' => __('validation.boolean', ['attribute' => strtolower(__('cruds.staff.fields.statement'))]),
        ];
    }
}
