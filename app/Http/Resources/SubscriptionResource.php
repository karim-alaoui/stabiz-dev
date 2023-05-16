<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SubscriptionResource
 * @package App\Http\Resources
 */
class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => __($this->name),
            'stripe_status' => __($this->stripe_status),
            'trail_ends_at' => $this->trail_ends_at,
            'ends_at' => $this->ends_at,
            'started_at' => $this->created_at, // created at is started at
            'subscriptionItem' => new SubscriptionItemResource($this->whenLoaded('subscriptionItem'))
        ];
    }
}
