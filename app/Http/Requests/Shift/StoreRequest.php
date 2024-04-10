<?php

namespace App\Http\Requests\Shift;

use App\Rules\UserHasRole;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        abort_if(Gate::denies('shift_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        $rules['shift_label'] = ['required', 'string','max:15'];

        if($this->has('sub_admin_id')){
            $rules['sub_admin_id'] = ['required', 'exists:users,uuid,deleted_at,NULL', new UserHasRole(config('constant.roles.sub_admin'), '', 'uuid')];
        }
        $rules['client_detail_id'] = ['required', 'exists:client_details,uuid,deleted_at,NULL'];

        $rules['start_date'] = ['required', 'date', function ($attribute, $value, $fail) {
            $today = Carbon::today();
            if (Carbon::parse($value)->lt($today)) {
                $fail('The ' . str_replace('_', ' ',$attribute) . ' must be greater than or equal to today.');
            }
        }];
        $rules['end_date'] = ['required', 'date', 'after_or_equal:start_date'];
        
        $rules['start_time'] = ['required', 'date_format:'.config('constant.date_format.time'), function ($attribute, $value, $fail) {
            $today = Carbon::today();
            $currentTime = Carbon::now()->format('H:i');
            if ($this->start_date === $today->format('d-m-Y') && $value < $currentTime) {
                $fail('The ' . str_replace('_', ' ',$attribute) . ' must be greater than or equal to the current time.');
            }
        }];
        $rules['end_time'] = ['required', 'date_format:'.config('constant.date_format.time'), function ($attribute, $value, $fail) {
            if($this->start_date == $this->end_date && $value < $this->start_time) {
                $fail('The ' . str_replace('_', ' ',$attribute) . ' must be greater than the start time.');
            }

            if($value == $this->start_time){
                $fail('The ' . str_replace('_', ' ',$attribute) . ' cannot be equal to the start time.');
            }
        }];

        // $rules['end_time'] = ['required', 'date_format:'.config('constant.date_format.time'), 'after:start_time'];
        

        $rules['location_id'] = ['required', 'exists:locations,uuid,deleted_at,NULL' ];
        $rules['occupation_id'] = ['required', 'exists:occupations,uuid,deleted_at,NULL'];
        $rules['assign_staff'] = ['nullable', 'exists:users,uuid,deleted_at,NULL', new UserHasRole(config('constant.roles.staff'), '', 'uuid')];

        return $rules;
    }

    public function messages()
    {
        return [
            'start_date.required' => 'The start date is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'end_date.required' => 'The end date is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'start_time.required' => 'The start time is required.',
            'start_time.date_format' => 'The start time must be in the format HH:MM.',
            'end_time.required' => 'The end time is required.',
            'end_time.date_format' => 'The end time must be in the format HH:MM.',
        ];
    }

    public function attributes()
    {
        return [
            'sub_admin_id' => 'Client name',
            'client_detail_id' => 'Client Detail Name',
            'location_id' => 'Location',
            'occupation_id' => ' Occupation',
        ];
    }
}
