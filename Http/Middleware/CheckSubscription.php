<?php

namespace App\Http\Middleware;

use App\Services\CompanyService;
use App\Services\StripeService;
use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    private $stripeService;
    private $companyService;

    public function __construct(StripeService $stripeService, CompanyService $companyService)
    {
        $this->stripeService = $stripeService;
        $this->companyService = $companyService;
    }

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
        if ($user->isManager() && ! $user->onGenericTrial() && ! $this->stripeService->checkIfUserHasValidSubscription($user)) {
            abort(403, trans('brikk.error.subscription_not_exists'));
        }
        if ($user->isConsultant() && ! $this->companyService->checkIfHasSubscribedUser($user->company_id)) {
            abort(403, trans('brikk.error.company_subscription_not_exists'));
        }

        return $next($request);
    }
}
