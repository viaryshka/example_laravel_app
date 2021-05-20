<?php

namespace App\Http\Resources;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Subscription $resource */
        $endsAt = $this->ends_at;

        /** @var User $user */
        $user = $this->user;

        $status = $this->getSubscriptionStatus();

        $now = \Dates::setTimezone(now());
        $trialEndsAt = \Dates::setTimezone($user->trial_ends_at);

        return [
            'tariff'                 => new TariffResource($this->tariff),
            'status'                 => $status,
            'next_payment_at'        => optional(\Dates::setTimezone($this->next_payment_at))->format('Y-m-d'),
            'current_period_ends_at' => optional(\Dates::setTimezone($this->current_period_ends_at))->format('Y-m-d'),
            $this->mergeWhen($endsAt && $status == Subscription::STATUS_ON_GRACE_PERIOD, [
                'ends_at' => optional(\Dates::setTimezone($endsAt))->format('Y-m-d'),
            ]),
            $this->mergeWhen($user->onGenericTrial(), [
                'trial_ends_at'    => optional($trialEndsAt)->format('Y-m-d'),
                'trial_days_count' => optional($trialEndsAt)->diffInDays($now),
            ]),
        ];
    }
}
