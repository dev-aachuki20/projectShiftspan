<?php

namespace App\Http\Requests\Location;

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
        abort_if(Gate::denies('location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => [
                'required',
                'string',
                // 'regex:/^[\pL\s\-]+$/u',
                'max:191',
                'unique:locations,name,NULL,id,deleted_at,NULL',
            ]
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'location name',
        ];
    }
}
