<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
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
            'seller'     => new UserResource($this->whenLoaded('seller')),
            'buyer'      => new UserResource($this->whenLoaded('buyer')),
            'property'   => new PropertyResource($this->whenLoaded('property')),
            'price'      => $this->price,
            'created_at' => \Dates::setTimezone($this->created_at)->format('Y-m-d'),
        ];
    }
}
