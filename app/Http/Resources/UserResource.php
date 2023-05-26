<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Models\FounderProfile;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $dob = $this->dob;
        $age = null;
        if ($dob) {
            $dob = Carbon::parse($dob)->format('Y-m-d');
            $now = Carbon::now();
            $age = $now->diffInYears($dob);
        }

        $entrProfile = $this->whenLoaded('entrProfileWithRelations');
        // if it was not loaded then it would return an empty object
        if ((array)$entrProfile == []) {
            $entrProfile = $this->whenLoaded('entrProfile');
        }

        if( $this->type == 'founder'){
            $fdrProfile = FounderProfile::join('founder_user', 'founder_profiles.id', '=', 'founder_user.founder_id')
            ->where('founder_user.user_id', $this->id)
            ->first();
        }

        // dp/profile picture
        $dp = $this->dp_full_path;
        $disk = $this->dp_disk;
        if ($disk && $dp) {
            $dp = Storage::disk($disk)->url($dp);
        }

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'first_name_cana' => $this->first_name_cana,
            'last_name_cana' => $this->last_name_cana,
            'gender' => $this->gender,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'dob' => $dob,
            'age' => $age,
            'income_range_id' => $this->income_range_id,
            'income' => new IncomeRangeResource($this->whenLoaded('income')),
            'avatar' => $dp,
            'type' => $this->type,
            'entrepreneur_profile' => new EntrProfileResource($entrProfile),
            'founder_profile' => new FounderProfileResource($fdrProfile),
            'assigned_staff' => AssignedUserToStaffResource::collection($this->whenLoaded('assignedStaff')),
            'created_at' => $this->created_at
        ];
    }
}
