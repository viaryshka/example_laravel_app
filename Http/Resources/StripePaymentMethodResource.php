<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Cashier\PaymentMethod;

class StripePaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var PaymentMethod $method */
        $method = $this->resource;
        /** @var User $owner */
        $owner = $method->owner();
        $customer = $owner->asStripeCustomer();
        $defaultId = $customer->invoice_settings->default_payment_method;
        $card = $method->asStripePaymentMethod()->card;
        $cardData = [
            'brand'     => $card->brand ?? null,
            'exp_month' => $card->exp_month ?? null,
            'exp_year'  => $card->exp_year ?? null,
            'last4'     => $card->last4 ?? null,
        ];
        $data = [
            'id'      => $method->id,
            'type'    => $method->type,
            'default' => $defaultId == $method->id,
        ];
        if ($method->type == 'card') {
            $data = array_merge($data, $cardData);
        }

        return $data;
    }
}
