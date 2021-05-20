<?php

namespace App\Broadcasting;

use App\Models\User;

class ChatBetweenUsersChannel
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
     * @param $id1
     * @param $id2
     * @return array|bool
     */
    public function join(User $user, $id1, $id2)
    {
        return (int) $user->id === (int) $id1 || (int) $user->id === (int) $id2;
    }
}
