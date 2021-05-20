<?php

namespace App\Broadcasting;

use App\Models\Chat;
use App\Models\User;

class ChatChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @param  Chat  $chat
     * @return array|bool
     */
    public function join(User $user, Chat $chat)
    {
        return $chat->hasUser($user);
    }
}
