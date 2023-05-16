<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PaymentMethodResource
 * @package App\Http\Resources
 */
class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $card = $this?->card;
        return [
            'id' => $this->id,
            'card' => [
                'brand' => $this?->card?->brand,
                'last4' => $card?->last4,
            ]
        ];
    }
}
