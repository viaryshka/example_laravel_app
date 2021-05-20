<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsFilled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->isFilled()) {
            abort(403, trans('brikk.error.user_not_filled'));
        }

        return $next($request);
    }
}
