<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
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
            'price_min'        => $this->price_min,
            'price_max'        => $this->price_max,
            'show_investments' => $this->show_investments,
            'regions'          => RegionResource::collection($this->regions),
            'cities'           => CityResource::collection($this->cities),
            'property_types'   => $this->getPropertyTypesArray(),
            'property_ages'    => $this->getPropertyAgesArray(),
        ];
    }
}
