<?php

namespace App\Http\Requests\SubAdmin;

use App\Rules\NoMultipleSpacesRule;
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
        abort_if(Gate::denies('sub_admin_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        $rules['name'] = ['required', new NoMultipleSpacesRule];

        $rules['email'] = ['required',"email:dns", 'unique:users,email,NULL,id,deleted_at,NULL'];

        $rules['password'] = ['required', 'string', 'min:8'];

        return $rules;
    }

    public function messages()
    {
        return [
            'password.regex' => 'The :attribute must be at least 8 characters and contain at least one uppercase character, one number, and one special character.',
        ];
    }
}
