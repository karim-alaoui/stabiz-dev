<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PackageResource
 * @package App\Http\Resources
 */
class PackageResource extends JsonResource
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
            'name' => __($this->name),
            'plans' => PlanResource::collection($this->whenLoaded('plans')),
            'monthly_plan' => new PlanResource($this->whenLoaded('monthlyPlan'))
        ];
    }
}
