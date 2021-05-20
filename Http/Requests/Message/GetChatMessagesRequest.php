<?php

namespace App\Http\Requests\Message;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetChatMessagesRequest extends FormRequest
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
        /** @var User $participant */
        $participant = $this->route('user');
        $userId = auth()->id();

        return [
            'last_message_id' => [Rule::exists('messages', 'id')->whereIn('user_id', [$participant->id, $userId])],
            'direction'       => [Rule::in(['next', 'prev'])],
        ];
    }
}
