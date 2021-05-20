<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'phone'        => 'numeric|nullable|digits_between:1,20',
            'company_name' => 'string|nullable|max:255',
            'lang'         => [Rule::in(\Languages::getListLanguages())],
        ];
        if (\Auth::user()->isManager()) {
            $arr['company_name'] = 'string|max:255';
        }

        return $arr;
    }
}
