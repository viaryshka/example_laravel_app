<?php

namespace App\Http\Requests\Social;

use App\Models\Role;
use App\Rules\UniqueActivatedEmailRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class FinishRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $arr = [
            'name'         => 'required|string|max:255',
            'email'        => [
                'required',
                'string',
                'email',
                'max:255',
                new UniqueActivatedEmailRule,
            ],
            'company_name' => 'string|nullable|max:255',
            'lang'         => [Rule::in(\Languages::getListLanguages())],
            'password'     => ['required', 'string', new Password, 'confirmed', 'max:255'],
            'role'         => ['required', Rule::in([Role::ROLE_MANAGER, Role::ROLE_CLIENT])],
        ];
        if (\Auth::user()->isManager()) {
            $arr['company_name'] = 'required|string|max:255';
        }

        return $arr;
    }
}
