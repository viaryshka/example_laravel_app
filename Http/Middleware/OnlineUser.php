<?php

namespace App\Http\Middleware;

use App\Models\User;
use Auth;
use Cache;
use Closure;
use Illuminate\Http\Request;

class OnlineUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $expiresAt = now()->addMinutes(10);
            Cache::put(User::onlineKey(Auth::id()), true, $expiresAt);
        }

        return $next($request);
    }
}
