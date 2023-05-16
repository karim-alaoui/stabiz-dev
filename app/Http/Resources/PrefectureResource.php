<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PrefectureResource
 * @package App\Http\Resources
 */
class PrefectureResource extends JsonResource
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
            'name' => $this->name_ja,
            'area_id' => $this->area_id,
            'area' => new AreaResource($this->whenLoaded('area'))
        ];
    }
}
