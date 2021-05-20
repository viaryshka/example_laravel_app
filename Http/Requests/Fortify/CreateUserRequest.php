<?php

namespace App\Http\Requests\Fortify;

use App\Models\Role;
use App\Models\User;
use App\Rules\UniqueActivatedEmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => [
                'required',
                'string',
                'email',
                'max:255',
                new UniqueActivatedEmailRule,
            ],
            'password'     => ['required', 'string', new Password, 'confirmed', 'max:255'],
            'role'         => ['required', Rule::in([Role::ROLE_MANAGER, Role::ROLE_CLIENT])],
            'lang'         => ['required', Rule::in(\Languages::getListLanguages())],
            'company_name' => ['required_if:role,'.Role::ROLE_MANAGER],
        ];
    }
}
