<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignedUserToStaffResource extends JsonResource
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
            'staff_id' => $this->staff_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'staff' => new AssignedStaffResource($this->whenLoaded('staff'))
        ];
    }
}
