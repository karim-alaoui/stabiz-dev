<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadedDocResource extends JsonResource
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
            'doc_name' => $this->doc_name,
            'user_id' => $this->user_id,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at
        ];
    }
}
