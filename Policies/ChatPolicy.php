<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can(Permission::CHAT_READ);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function view(User $user, Chat $chat)
    {
        return $chat->hasUser($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(Permission::CHAT_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function update(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function delete(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function restore(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return mixed
     */
    public function forceDelete(User $user, Chat $chat)
    {
        //
    }
}
