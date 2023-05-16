<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PositionResource
 * @package App\Http\Resources
 */
class OccupationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => __($this->name),
            'occupation_category_id' => $this->occupation_category_id,
            'category' => new OccupationCatResource($this->whenLoaded('category'))
        ];
    }
}
