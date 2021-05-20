<?php

namespace App\Http\Resources;

use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'id'          => $this->id,
            'status'      => $this->status,
            $this->mergeWhen(\Auth::user()->can(Permission::USER_PREFERENCE_UPDATE), [
                'is_actual'   => $this->is_actual,
            ]),
            'company'     => new CompanyResource($this->whenLoaded('company')),
            'user'        => new UserResource($this->whenLoaded('user')),
            'property_ad' => new PropertyAdResource($this->whenLoaded('propertyAd'), $this->showContact()),

            $this->mergeWhen(! is_null($this->prev), [
                'prev' => $this->prev->id ?? null,
            ]),
            $this->mergeWhen(! is_null($this->next), [
                'next' => $this->next->id ?? null,
            ]),
        ];
    }
}
