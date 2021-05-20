<?php

namespace App\Http\Requests\Profile;

use App\Models\Attachment;
use Illuminate\Foundation\Http\FormRequest;

class UploadAvatarRequest extends FormRequest
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
            'avatar' => 'required|file|max:2048|mimes:'.implode(',', Attachment::allowedImages()),
        ];
    }
}
