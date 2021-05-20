<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Languages;

class Localization
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
        $user = \Auth::user();
        if ($user) {
            Languages::checkLang($user->lang);
        }
        if ($request->hasHeader('X-localization')) {
            Languages::checkLang($request->header('X-localization'));
        }

        return $next($request);
    }
}
