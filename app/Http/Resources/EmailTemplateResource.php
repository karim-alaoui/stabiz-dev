<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EmailTemplateResource
 * @package App\Http\Resources
 */
class EmailTemplateResource extends JsonResource
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
//            'name' => __($this->name),
            'subject' => $this->subject,
            'body' => $this->body,
            'comment' => $this->comment
        ];
    }
}
