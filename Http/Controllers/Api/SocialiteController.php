<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Social\FinishRegisterRequest;
use App\Http\Requests\Social\GetProviderUrlRequest;
use App\Http\Requests\Social\SocialBindRequest;
use App\Http\Requests\Social\SocialLoginRequest;
use App\Http\Requests\Social\SocialRegisterRequest;
use App\Http\Resources\PreRegisterUserResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\SocialAccountResource;
use App\Models\SocialAccount;
use App\Models\User;
use App\Services\SocialService;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;

class SocialiteController extends Controller
{
    private $socialService;
    private $userService;
    private $guard;

    public function __construct(SocialService $socialService, UserService $userService, StatefulGuard $guard)
    {
        $this->socialService = $socialService;
        $this->userService = $userService;
        $this->guard = $guard;
    }

    /**
     * @param  GetProviderUrlRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUrl(GetProviderUrlRequest $request)
    {
        $provider = $request->provider;
        $redirectUri = $request->redirect_uri ?? false;
        $url = $this->socialService->getProviderUrl($provider, $redirectUri);
        if (! $url) {
            abort(400);
        }

        return response()->json(['data' => ['url' => $url]]);
    }

    /**
     * @param  SocialLoginRequest  $request
     * @return ProfileResource
     * @throws ValidationException
     */
    public function login(SocialLoginRequest $request)
    {
        $user = $this->socialService->getUserFromSocialite($request->provider);
        if ($user instanceof User) {
            $this->guard->login($user);
        } else {
            throw ValidationException::withMessages($user);
        }

        return new ProfileResource($user);
    }

    /**
     * @param  SocialRegisterRequest  $request
     * @return PreRegisterUserResource
     * @throws ValidationException
     */
    public function registerStep1(SocialRegisterRequest $request)
    {
        $user = $this->socialService->createUserFromSocialite($request->provider);
        if ($user instanceof User) {
            $this->guard->login($user);
        } else {
            throw ValidationException::withMessages($user);
        }

        return new PreRegisterUserResource($user);
    }

    /**
     * @param  FinishRegisterRequest  $request
     * @return ProfileResource
     */
    public function registerStep2(FinishRegisterRequest $request)
    {
        $keys = [
            'email',
            'name',
            'lang',
            'password',
            'company_name',
            'role',
        ];
        $user = $this->userService->update(\Auth::user(), $request->only($keys));
        event(new Registered($user));

        return new ProfileResource($user);
    }

    /**
     * @param  SocialBindRequest  $request
     * @return SocialAccountResource
     * @throws ValidationException
     */
    public function bind(SocialBindRequest $request)
    {
        $user = \Auth::user();
        $socialAccount = $this->socialService->createSocialAccountFromSocialite($user, $request->provider);
        if (is_array($socialAccount)) {
            throw ValidationException::withMessages($socialAccount);
        }

        return new  SocialAccountResource($socialAccount);
    }

    /**
     * @param  SocialAccount  $socialAccount
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(SocialAccount $socialAccount)
    {
        $this->authorize('delete', $socialAccount);

        $deleted = $this->socialService->delete($socialAccount);
        if (! $deleted) {
            abort(400);
        }

        return response()->noContent();
    }
}
