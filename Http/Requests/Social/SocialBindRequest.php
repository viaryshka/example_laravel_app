<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialBindRequest extends FormRequest
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
        return [
            'provider' => ['required', Rule::in(config('social.providers'))],
        ];
    }
}