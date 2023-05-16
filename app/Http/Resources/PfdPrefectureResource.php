<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PfdPrefectureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'prefecture' => new PrefectureResource($this->whenLoaded('prefecture'))
        ];
    }
}
