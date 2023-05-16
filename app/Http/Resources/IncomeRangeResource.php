<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class IncomeResource
 * @package App\Http\Resources
 */
class IncomeRangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'lower_limit' => $this->lower_limit,
            'upper_limit' => $this->upper_limit,
            'unit' => __($this->unit),
            'currency' => $this->currency,
            'is_lowest_limit' => $this->is_lowest_limit,
            'is_highest_limit' => $this->is_highest_limit
        ];
    }
}
