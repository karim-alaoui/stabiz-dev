<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PlanResource
 * @package App\Http\Resources
 */
class PlanResource extends JsonResource
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
            'price' => $this->price,
            'currency' => $this->currency,
            'interval' => $this->interval,
            'package' => new PackageResource($this->whenLoaded('package'))
        ];
    }
}
