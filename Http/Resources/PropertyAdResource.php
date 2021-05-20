<?php

namespace App\Http\Resources;

use App\Models\PropertyAd;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyAdResource extends JsonResource
{
    private $showUser;

    public function __construct($resource, $showUser = false)
    {
        parent::__construct($resource);
        $this->showUser = $showUser;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $type = $this->type;
        if ($type == PropertyAd::TYPE_INVESTMENT) {
            $resource = [
                'id'                  => $this->id,
                'status'              => $this->status,
                'type'                => $this->type,
                'goal'                => $this->goal->value ?? null,
                'expectation'         => $this->expectation,
                'loan_type'           => $this->loanType->value ?? null,
                'buying_entity'       => $this->buyingEntity->value ?? null,
                'first_time_investor' => $this->first_time_investor,
            ];
        } else {
            $resource = [
                'id'               => $this->id,
                'status'           => $this->status,
                'type'             => $this->type,
                'price_min'        => $this->price_min,
                'price_max'        => $this->price_max,
                'property_types'   => $this->getPropertyTypesArray(),
                'property_ages'    => $this->getPropertyAgesArray(),
                'regions'          => RegionResource::collection($this->whenLoaded('regions')),
                'cities'           => CityResource::collection($this->whenLoaded('cities')),
                'min_size'         => $this->min_size,
                'bedrooms'         => $this->bedrooms,
                'bathrooms'        => $this->bathrooms,
                'parking'          => $this->parking->value ?? null,
                'min_size_garden'  => $this->min_size_garden,
                'min_size_terrace' => $this->min_size_terrace,
                'outside_facing'   => $this->outsideFacing->value ?? null,
                'floor'            => $this->floor->value ?? null,
            ];
        }

        if ($this->showUser) {
            $resource['user'] = new UserResource($this->whenLoaded('user'));
        }
        $resource['created_at'] = \Dates::setTimezone($this->created_at)->format('Y-m-d H:i');

        return $resource;
    }
}
