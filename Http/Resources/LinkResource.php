<?php

namespace App\Http\Resources;

use App\Models\Link;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'sender'     => new UserResource($this->whenLoaded('sender')),
            'receiver'   => new UserResource($this->whenLoaded('receiver')),
            $this->mergeWhen(\Auth::user()->can('view', $this->whenLoaded('property')), [
                'property' => new PropertyResource($this->whenLoaded('property')),
            ]),
            $this->mergeWhen(\Auth::id() == $this->receiver_id && $this->price_status === Link::PRICE_STATUS_CONFIRMED,
                [
                    'price' => $this->price,
                ]),
            $this->mergeWhen(\Auth::user()->can('viewPriceStatus', Link::class), [
                'price'          => $this->price ?? null,
                'property_price' => $this->getPropertyPrice(),
                'difference'     => $this->getDifference(),
                'price_status'   => $this->price_status,
            ]),
            'lead'       => new LeadResource($this->whenLoaded('lead')),
            'status'     => $this->status,
            $this->mergeWhen(\Auth::id() == $this->receiver_id, [
                'seen' => $this->seen,
            ]),
            'created_at' => \Dates::setTimezone($this->created_at)->format('Y-m-d'),
        ];
    }
}
