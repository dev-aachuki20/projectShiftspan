<?php

namespace App\Http\Requests\Occupation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class OccupationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        abort_if(Gate::denies('occupation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ids = $this->input('ids');
        
        if (!empty($ids)) {
            /* For Delete Multiple Data */
            $rules = [
                'ids'   => 'required|array',
                'ids.*' => 'exists:occupations,uuid',
            ];
        }elseif(isset($this->occupation)){
            /* For Update the Value */
            $rules = [
                'name'    => [
                    'required',
                    'string',
                    'max:255',
                    'unique:occupations,name,'. $this->occupation.',uuid,deleted_at,NULL',
                ],
            ];
        }else{
            /* For Create the Value */
            $rules = [
                'name'     => [
                    'required',
                    'string',
                    'max:255',
                    'unique:occupations,name,NULL,id,deleted_at,NULL',
                ],
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('validation.required', ['attribute' => strtolower(__('cruds.occupation.fields.occupation_name'))]),
            'name.string' => __('validation.string', ['attribute' => strtolower(__('cruds.occupation.fields.occupation_name'))]),
            'name.unique' => __('validation.unique', ['attribute' => strtolower(__('cruds.occupation.fields.occupation_name'))]),
            'name.max' => __('validation.max.string', ['attribute' => strtolower(__('cruds.occupation.fields.occupation_name')), 'max' => ':max']),
        ];
    }
}
