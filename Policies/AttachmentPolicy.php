<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  Attachment  $attachment
     * @return mixed
     */
    public function delete(User $user, Attachment $attachment)
    {
        return $attachment->user_id === $user->id;
    }
}
