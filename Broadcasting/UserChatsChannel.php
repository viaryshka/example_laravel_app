<?php

namespace App\Broadcasting;

use App\Models\User;

class UserChatsChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @param $id
     * @return array|bool
     */
    public function join(User $user, $id)
    {
        return (int) $user->id === (int) $id;
    }
}
