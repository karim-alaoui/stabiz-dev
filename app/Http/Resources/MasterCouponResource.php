<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterCouponResource
 * @package App\Http\Resources
 */
class MasterCouponResource extends JsonResource
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
            'name' => $this->name,
            'amount_off' => $this->amount_off,
            'percent_off' => $this->percent_off,
            'currency' => $this->currency,
            'duration' => $this->duration,
            'duration_in_months' => $this->duration_in_months,
            'redeem_by' => $this->redeem_by,
            'assign_after' => $this->assign_after,
            'is_a_campaign' => $this->is_a_campaign
        ];
    }
}
