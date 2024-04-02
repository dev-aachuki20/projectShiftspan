<?php

namespace App\Http\Requests\SubAdmin;

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
        abort_if(Gate::denies('sub_admin_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        // $rules['name'] = ['required', 'regex:/^[a-zA-Z\s]+$/', 'string', 'max:255', new NoMultipleSpacesRule];
        $rules['name'] = ['required', 'regex:/^[a-zA-Z\s\-\'\.\,\(\)\[\]\{\}\<\>\*\&\^\%\$\#\@\!\~\`\|\+\=\;\:\?\"\\\©]+$/u', 'string', 'max:255', new NoMultipleSpacesRule];

        
        return $rules;
    }

    public function attributes()
    {
        return [
            
        ];
    }
}
