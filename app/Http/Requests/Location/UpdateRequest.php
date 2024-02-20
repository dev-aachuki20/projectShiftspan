<?php

namespace App\Http\Requests\Location;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        abort_if(Gate::denies('location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        if($this->has('name')){
            $rules['name'] = ['required','string',new NoMultipleSpacesRule,'max:191','unique:locations,name,'. $this->location.',uuid,deleted_at,NULL'];
        }
        if($this->has('sub_admin')){
            $rules['sub_admin'] = ['nullable','array'];
            $rules['sub_admin.*'] = ['exists:users,uuid'];
        }
        if($this->has('location_name')){
            $rules['location_name'] = ['required','integer','exists:locations,uuid'];
        }
        
        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'location name',
            'sub_admin.*' => 'sub admin',
        ];
    }
}
