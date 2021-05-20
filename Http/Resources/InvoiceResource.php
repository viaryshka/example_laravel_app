<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'id'                    => $this->id,
            'number'                => $this->number,
            'total'                 => $this->total / 100,
            'subtotal'              => $this->subotal / 100,
            'customer_name'         => $this->customer_name,
            'customer_email'        => $this->customer_email,
            'customer_phone'        => $this->customer_phone,
            'customer_company'      => $this->customer_company,
            'customer_address_city' => $this->customer_address_city,
            'customer_address_line' => $this->customer_address_line,
            'customer_address_zip'  => $this->customer_address_zip,
            'card_brand'            => $this->card_brand,
            'card_last4'            => $this->card_last4,
            'created_at'            => \Dates::setTimezone($this->created_at)->format('Y-m-d'),
            'lines'                 => InvoiceLineResource::collection($this->whenLoaded('lines')),
        ];
    }
}
