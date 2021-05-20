<?php

namespace App\Http\Resources;

use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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

        $project = $this->whenLoaded('project');
        $projectCompanyId = $project->user->company_id ?? null;

        $canReadFavs = $user->can(Permission::PROPERTY_READ_FAVORITES);
        $canAddFavs = $user->can(Permission::PROPERTY_ADD_FAVORITE);
        $canReadLinkedFavs = $user->can(Permission::PROPERTY_LINK_READ_FAVORITES);
        $canReadOtherLinks = $user->can(Permission::LINK_READ_OTHER);
        $canCreateLinks = $user->can(Permission::LINK_CREATE);
        $canReadLeads = $user->can(Permission::LEAD_READ) || $user->can(Permission::LEAD_READ_OTHER);
        $hasRequestedLinkReceiver = $this->hasRequestedLinkReceiver();
        $hasRequestedLead = $this->hasRequestedLead();

        $deal = $this->whenLoaded('deal');

        if ($user->can(Permission::DEAL_BUY)) {
            $isMyBoughtDeal = $this->isMyBoughtDeal();
        } else {
            $isMyBoughtDeal = null;
        }

        return [
            'id'                 => $this->id,
            $this->mergeWhen($projectCompanyId == $user->company_id && \Auth::user()->can(Permission::PROJECT_READ), [
                'project' => new ProjectResource($project),
            ]),
            'city'               => new CityResource($this->whenLoaded('city')),
            'address'            => $this->address,
            'zip'                => $this->zip,
            'unit'               => $this->unit,
            'description'        => $this->description,
            'price'              => $this->price,
            'lat'                => $this->lat,
            'lng'                => $this->lng,
            'avatar'             => optional($this->avatar)->getUrl(),
            'size'               => $this->size,
            'bedrooms'           => $this->bedrooms,
            'bathrooms'          => $this->bathrooms,
            'garages'            => $this->garages,
            'year'               => $this->year,
            'size_garden'        => $this->size_garden,
            'size_terrace'       => $this->size_terrace,
            'epc'                => $this->epc,
            'garden_orientation' => $this->garden_orientation,
            'available_from'     => optional($this->available_from)->format('Y-m-d'),
            'images'             => AttachmentResource::collection($this->whenLoaded('images')),
            'status'             => $this->status,
            $this->mergeWhen($user->can(Permission::LINK_RECEIVE), [
                'myReceivedLink' => new LinkResource($this->whenLoaded('myReceivedLink')),
                'owned'          => $this->user_id == $user->id,
            ]),
            $this->mergeWhen($deal && $user->can('view', $deal), [
                'deal' => new DealResource($deal),
            ]),
            $this->mergeWhen($user->can(Permission::PROPERTY_SET_VISIBILITY) && $this->user_id == $user->id, [
                'enabled' => $this->enabled,
            ]),
            $this->mergeWhen(! is_null($isMyBoughtDeal), [
                'is_my_bought_deal' => $isMyBoughtDeal,
            ]),
            $this->mergeWhen($canAddFavs, [
                'is_favorite' => $this->isMyFavorite($canAddFavs),
            ]),
            $this->mergeWhen($canReadFavs, [
                'total_favorites_count' => $this->getUsersFavoritesCount($canReadFavs),
            ]),
            $this->mergeWhen($canReadLinkedFavs, [
                'linked_favorites_count' => $this->getMyLinkedUsersFavoritesCount($canReadLinkedFavs),
            ]),
            $this->mergeWhen($canReadOtherLinks, [
                'links_count' => $this->getLinksCount($canReadOtherLinks),
            ]),
            $this->mergeWhen($canCreateLinks && ! is_null($hasRequestedLinkReceiver), [
                'has_link' => $hasRequestedLinkReceiver,
            ]),
            $this->mergeWhen($canReadLeads && ! is_null($hasRequestedLead), [
                'has_lead' => $hasRequestedLead,
            ]),
        ];
    }
}
