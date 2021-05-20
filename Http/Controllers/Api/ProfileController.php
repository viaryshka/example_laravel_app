<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfilePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Permission;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private $userService;
    private $attachmentService;

    public function __construct(UserService $userService, AttachmentService $attachmentService)
    {
        $this->userService = $userService;
        $this->attachmentService = $attachmentService;
    }

    /**
     * Get profile.
     *
     * @param  Request  $request
     * @return ProfileResource
     */
    public function index(Request $request)
    {
        $user = \Auth::user();

        return new ProfileResource($user);
    }

    /**
     * @param  UpdateProfileRequest  $request
     * @return ProfileResource
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = \Auth::user();
        $user = $this->userService->update($user, $request->only(['name', 'phone', 'company_name', 'lang']));
        $user->loadMissing('company');

        return new ProfileResource($user);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = \Auth::user();
        $deleted = $this->userService->delete($user);
        if (! $deleted) {
            abort(400);
        }

        return response()->noContent();
    }

    /**
     * @param  UpdateProfilePasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(UpdateProfilePasswordRequest $request)
    {
        $user = \Auth::user();
        $this->userService->update($user, $request->only(['password']));

        return response()->noContent();
    }


}
