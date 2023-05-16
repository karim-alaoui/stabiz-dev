<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NewsTopicResource
 * @package App\Http\Resources
 */
class NewsTopicResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'show_after' => $this->show_after,
            'hide_after' => $this->hide_after,
            'created_at' => $this->created_at
        ];
    }
}
