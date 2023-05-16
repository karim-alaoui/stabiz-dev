<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecommendationResource extends JsonResource
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
            'recommended_user_id' => $this->recommended_user_id,
            'recommended_to_user_id' => $this->recommended_to_user_id,
            'recommended_user' => new UserResource($this->whenLoaded('recommendedUser')),
            'recommended_to_user' => new UserResource($this->whenLoaded('recommendedToUser'))
        ];
    }
}
