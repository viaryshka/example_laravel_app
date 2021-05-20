<?php

namespace App\Http\Resources;

use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = \Auth::user();

        $canReadLinkedFavs = $user->can(Permission::PROPERTY_LINK_READ_FAVORITES);
        $canReadFavs = $user->can(Permission::PROPERTY_READ_FAVORITES);
        $canReadChats = $user->can(Permission::CHAT_READ);
        $canReceiveLinks = $user->can(Permission::LINK_RECEIVE);
        $canReadCredit = $user->can(Permission::CREDIT_READ);
        $canBuySubscription = $user->can(Permission::SUBSCRIPTION_BUY);

        return [
            'id'              => $this->id,
            'email'           => $this->email,
            'name'            => $this->name,
            'role'            => $this->getRoleName(),
            'phone'           => $this->phone,
            'lang'            => $this->lang,
            'company'         => new CompanyResource($this->whenLoaded('company')),
            'avatar'          => optional($this->avatar)->getUrl(),
            'filled'          => $this->isFilled(),
            'email_verified'  => $this->hasVerifiedEmail(),
            'created_at'      => \Dates::setTimezone($this->created_at)->format('Y-m-d'),
            'social_accounts' => SocialAccountResource::collection($this->whenLoaded('socialAccounts')),

            $this->mergeWhen($canReadLinkedFavs, [
                'top_linked_properties' => PropertyResource::collection($this->whenLoaded('topLinkedProperties')),
            ]),
            $this->mergeWhen($canReadFavs, [
                'top_properties' => PropertyResource::collection($this->whenLoaded('topProperties')),
            ]),

            $this->mergeWhen($canReadChats, [
                'unread_messages_count' => $this->getUnreadMessagesCount(),
            ]),
            $this->mergeWhen($canReceiveLinks, [
                'new_links_count' => $this->getNewReceivedLinksCount(),
            ]),
            $this->mergeWhen($canReadCredit, [
                'credits' => $this->credits,
            ]),
            $this->mergeWhen($canBuySubscription, [
                'subscription' => new SubscriptionResource($this->getSubscriptionOrTrial()),
            ]),
            $this->mergeWhen($this->isConsultant(), [
                'has_subscription' => $this->hasCompanyWithSubscription(),
            ]),
        ];
    }
}
