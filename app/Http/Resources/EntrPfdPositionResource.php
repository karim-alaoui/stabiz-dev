<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntrPfdPositionResource extends JsonResource
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
            'entrepreneur_profile_id' => $this->entrepreneur_profile_id,
            'position_id' => $this->position_id,
            'position' => new PositionResource($this->whenLoaded('position'))
        ];
    }
}
