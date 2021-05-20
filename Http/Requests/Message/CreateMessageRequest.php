<?php

namespace App\Http\Requests\Message;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMessageRequest extends FormRequest
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
        $fromUser = \Auth::user();

        return [
            'body'          => 'required_without:attachments|string|max:10000',
            'attachments'   => 'required_without:body|array|min:0',
            'attachments.*' => [
                'required',
                'distinct',
                Rule::exists('attachments', 'id')->where(function ($query) use ($fromUser) {
                    $query->where('user_id', $fromUser->id)
                          ->whereNull('attachable_type')
                          ->where('type', Attachment::TYPE_UPLOADED_FOR_MESSAGE);
                }),
            ],
        ];
    }
}
