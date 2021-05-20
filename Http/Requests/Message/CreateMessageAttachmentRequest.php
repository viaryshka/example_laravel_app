<?php

namespace App\Http\Requests\Message;

use App\Models\Attachment;
use Illuminate\Foundation\Http\FormRequest;

class CreateMessageAttachmentRequest extends FormRequest
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
        $formats = Attachment::allowedAttachments();

        return [
            'attachment' => 'required|file|max:15360|mimes:'.implode(',', $formats),
        ];
    }
}
