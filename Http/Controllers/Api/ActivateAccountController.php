<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Referral\ActivateAccountRequest;
use App\Services\UserService;
use Illuminate\Contracts\Auth\StatefulGuard;

class ActivateAccountController extends Controller
{
    private $userService;
    private $guard;

    public function __construct(UserService $userService, StatefulGuard $guard)
    {
        $this->userService = $userService;
        $this->guard = $guard;
    }

    /**
     * Handle the incoming request.
     *
     * @param  ActivateAccountRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(ActivateAccountRequest $request)
    {
        $user = $this->userService->activateAccount($request->route('id'), $request->only(['password', 'name', 'lang']));
        if (! $user) {
            return response()->json([
                'message' => trans('brikk.error.account_activate_error'),
            ], 404);
        }
        $this->guard->login($user);

        return response()->noContent();
    }
}
