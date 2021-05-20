<?php

namespace App\Http\Controllers\Fortify;

use App\Http\Requests\Fortify\CreateUserRequest;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\RegisterResponse;

class RegisteredUserController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Create a new registered user.
     *
     * @param  \App\Http\Requests\Fortify\CreateUserRequest  $request
     * @param  \App\Services\UserService  $service
     * @return \Laravel\Fortify\Contracts\RegisterResponse
     */
    public function store(
        CreateUserRequest $request,
        UserService $service
    ): RegisterResponse {
        $user = $service->create($request->only(['email', 'name', 'password', 'role', 'lang', 'company_name']));
        event(new Registered($user));

        return app(RegisterResponse::class);
    }
}
