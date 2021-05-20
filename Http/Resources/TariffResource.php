<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TariffResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'alias'         => $this->alias,
            'period'        => $this->period,
            'price'         => $this->price,
            'price_monthly' => $this->price_monthly,
            'bonus_text'    => $this->bonus_text,
            'benefits_text' => $this->benefits_text,
            'modules_text'  => $this->modules_text,
        ];
    }
}
