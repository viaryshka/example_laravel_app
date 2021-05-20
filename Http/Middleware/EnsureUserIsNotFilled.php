<?php

namespace App\Http\Middleware;

use Closure;

class EnsureUserIsNotFilled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next)
    {
        if (! $request->user() || $request->user()->isFilled()) {
            abort(403, trans('brikk.error.user_filled'));
        }

        return $next($request);
    }
}
