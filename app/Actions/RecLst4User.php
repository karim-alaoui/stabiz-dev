<?php

namespace App\Actions;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Actions\ApplicationStatus;
use App\Models\FounderProfile;
use Illuminate\Support\Facades\DB;


/**
 * Recommended user list for the user
 *
 * A user can see his/her recommendation list a.k.a
 * users recommended to him/her
 */
class RecLst4User
{
    public static function execute($userId, $userType, array $query = [])
    {
        $recommendations = Recommendation::query()
            ->where('recommended_to_user_id', $userId)
            ->latest()
            ->get();
    
        $userIds = $recommendations->pluck('recommended_user_id')->toArray();
    
        $applicationStatus = new ApplicationStatus();
        $applicationStatuses = $applicationStatus->processApplications($userId, $userIds);
    
        $recommendations = $recommendations->map(function ($recommendation) use ($applicationStatuses, $userType) {
            $userId = $recommendation->recommended_user_id;
            $applicationStatus = $applicationStatuses->firstWhere('other_user_id', $userId);
    
            $recommendedUser = null;
            if ($userType === 'entrepreneur') {
                $recommendedUser = FounderProfile::select('id', 'company_name', 'no_of_employees', 'is_listed_company', 'area_id', 'offered_income_range_id')
                    ->with(['area', 'industries', 'offeredIncome'])
                    ->find($userId);
            } elseif ($userType === 'founder') {
                $recommendedUser = User::where('type', User::ENTR)
                    ->with([
                        'income',
                        'entrProfile' => function ($query) {
                            $query->select('id', 'user_id', 'area_id')->with(['area', 'industriesPfd', 'positionsPfd']);
                        }
                    ])
                    ->select('users.id', 'users.first_name', 'income_range_id', DB::raw("date_part('year',age(users.dob)) AS age"))
                    ->find($userId);
            }
    
            $recommendation->recommended_user = $recommendedUser;
            $recommendation->application_status = $applicationStatus;
    
            return $recommendation;
        });
    
        return $recommendations;
    }
    
}