<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ArticleResource
 * @package App\Http\Resources
 */
class ArticleResource extends JsonResource
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
            'content' => $this->content,
            'description' => $this->description,
            'publish_after' => $this->publish_after,
            'hide_after' => $this->hide_after,
            'is_draft' => $this->is_draft,
            'audiences' => ArticleAudienceResource::collection($this->whenLoaded('audiences')),
            'tags' => ArticleTagResource::collection($this->whenLoaded('tags')),
            'categories' => ArticleCategoryResource::collection($this->whenLoaded('categories')),
            'industries' => ArticleIndustryResource::collection($this->whenLoaded('industries'))
        ];
    }
}
