<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultantResource extends JsonResource
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
            'email'      => $this->email,
            'name'       => $this->name,
            'phone'      => $this->phone,
            'avatar'     => optional($this->avatar)->getUrl(),
            'created_at' => \Dates::setTimezone($this->created_at)->format('Y-m-d'),
        ];
    }
}
