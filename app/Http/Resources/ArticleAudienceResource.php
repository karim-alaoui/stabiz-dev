<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ArticleAudienceResource
 * @package App\Http\Resources
 */
class ArticleAudienceResource extends JsonResource
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
            'article_id' => $this->article_id,
            'audience' => __($this->audience),
            'article' => new ArticleResource($this->whenLoaded('article'))
        ];
    }
}
