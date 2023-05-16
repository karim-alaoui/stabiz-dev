<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Assigned users to staff
 * Class AssignedUsers
 * @package App\Http\Resources
 */
class AssignedUserResource extends JsonResource
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
            'staff' => new StaffResource($this->whenLoaded('staff')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at
        ];
    }
}
