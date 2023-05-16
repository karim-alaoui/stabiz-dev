<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SubscriptionItemResource
 * @package App\Http\Resources
 */
class SubscriptionItemResource extends JsonResource
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
            'subscription_id' => $this->subscription_id,
            'package' => new PackageResource($this->whenLoaded('package')),
            'plan' => new PlanResource($this->whenLoaded('plan')),
        ];
    }
}
