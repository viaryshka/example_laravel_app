<?php

namespace App\Http\Controllers\Fortify;

use App\Http\Requests\Fortify\VerifyEmailRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \App\Http\Requests\Fortify\VerifyEmailRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(VerifyEmailRequest $request): RedirectResponse
    {
        $authUser = \Auth::user();
        $user = User::find($request->id);

        if ($authUser && ($authUser->id != $user->id)) {
            return redirect()->intended(config('app.front_url'));
        }

        if (! $user) {
            return redirect()->intended(config('app.front_url'));
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(config('app.front_url').'?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(config('app.front_url').'?verified=1');
    }
}
