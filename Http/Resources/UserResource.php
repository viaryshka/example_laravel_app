<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'               => $this->id,
            'email'            => $this->email,
            'name'             => $this->name,
            'phone'            => $this->phone,
            'company'          => new CompanyResource($this->whenLoaded('company')),
            'avatar'           => optional($this->avatar)->getUrl(),
            $this->mergeWhen($this->referral_id == auth()->id(), [
                'activated' => $this->activated,
            ]),
            'created_at'       => \Dates::setTimezone($this->created_at)->format('Y-m-d'),
            'myCompanySenders' => UserResource::collection($this->whenLoaded('myCompanySenders')),
            'is_online'        => $this->isOnline(),
        ];
    }
}
