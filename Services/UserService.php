<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    private $userRepo;

    /**
     * UserService constructor.
     *
     * @param  \App\Repositories\UserRepository  $userRepo
     * @param  AttachmentRepository  $attachmentRepo
     * @param  SubscriptionRepository  $subscriptionRepo
     */
    public function __construct(
        UserRepository $userRepo,
    ) {
        $this->userRepo = $userRepo;
    }

    /**
     * create new user.
     *
     * @param  array  $input
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $input)
    {
        return $this->userRepo->create($input);
    }

    /**
     *  update user.
     *
     * @param  User  $user
     * @param  array  $input
     * @return User
     */
    public function update(User $user, array $input)
    {
        $user = $this->userRepo->update($user, $input);
        if (array_key_exists('company_name', $input) && \Auth::user()->can(Permission::COMPANY_CREATE)) {
            $this->userRepo->editCompany($user, $input['company_name']);
        }
        if (array_key_exists('role', $input)) {
            $this->userRepo->syncRoles($user, [$input['role']]);
        }

        return $user;
    }


    /**
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user)
    {
        $this->subscriptionRepo->cancelSubscriptionForUser($user, true);

        return $this->userRepo->delete($user);
    }


}
