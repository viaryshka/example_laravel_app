<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Traits\Includable;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use Includable;

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->authorizeResource(User::class);
        $this->userService = $userService;
    }

    /**
     * Get profile.
     *
     * @param  User  $user
     * @param  Request  $request
     * @return UserResource
     */
    public function show(User $user, Request $request)
    {
        $user->loadMissing($this->getIncludesFromRequest($request));

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $deleted = $this->userService->delete($user);
        if (! $deleted) {
            abort(400);
        }

        return response()->noContent();
    }

    public function getAvailableIncludes()
    {
        return ['company'];
    }
}
