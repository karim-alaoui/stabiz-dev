<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EntrProfileResource
 * @package App\Http\Resources
 */
class EntrProfileResource extends JsonResource
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
            'address' => $this->address,
            'education_background' => new EduBgResource($this->whenLoaded('eduBg')),
            'prefecture' => new PrefectureResource($this->whenLoaded('prefecture')),
            'area' => new AreaResource($this->whenLoaded('area')),
            'school_name' => $this->school_name,
            'working_status' => new WorkingStatusResource($this->whenLoaded('workingStatus')),
            'present_post' => new PresentPostResource($this->whenLoaded('presentPost')),
            'present_post_other' => $this->present_post_other,
            'occupation' => new PositionResource($this->whenLoaded('occupation')),
            'present_company' => $this->present_company,
            'lang' => new LanguageResource($this->whenLoaded('lang')),
            'lang_other' => $this->lang_other,
            'lang_ability' => new LangLevelResource($this->whenLoaded('langAbility')),
            'en_lang_ability' => new LangLevelResource($this->whenLoaded('engLangAbility')),
            'transfer' => $this->transfer ? __($this->transfer) : null,
            'expected_income' => new IncomeRangeResource($this->whenLoaded('expectedIncome')),
            'industries_exp' => IndustryResource::collection($this->whenLoaded('industriesExp')),
            'prefectures_pfd' => PrefectureResource::collection($this->whenLoaded('prefecturesPfd')),
            'industries_pfd' => IndustryResource::collection($this->whenLoaded('industriesPfd')),
            'positions_pfd' => PositionResource::collection($this->whenLoaded('positionsPfd')),
            'work_start_date' => $this->work_start_date,
            'school_major' => $this->school_major,
            'management_exp' => new MgmtExpResource($this->whenLoaded('managementExp'))
        ];
    }
}
