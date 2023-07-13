<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;

class ApplicationStatus
{
    public function processApplications($currentUserId, $userIds)
    {
        $applications = DB::table('applications')
            ->select(
                DB::raw("CASE
                    WHEN applied_to_user_id = $currentUserId THEN applied_by_user_id::varchar
                    WHEN applied_by_user_id = $currentUserId THEN applied_to_user_id::varchar
                    ELSE ''::varchar
                END AS other_user_id"),
                DB::raw("CASE
                    WHEN applied_to_user_id = $currentUserId THEN 'received'
                    WHEN applied_by_user_id = $currentUserId THEN 'sent'
                    ELSE NULL
                END AS type"),
                DB::raw("CASE
                    WHEN accepted_at IS NOT NULL THEN 'accepted'
                    WHEN rejected_at IS NOT NULL THEN 'rejected'
                    ELSE 'pending'
                END AS status")
            )
            ->where(function ($query) use ($currentUserId, $userIds) {
                $query->whereIn('applied_by_user_id', [$currentUserId, ...$userIds])
                    ->whereIn('applied_to_user_id', [$currentUserId, ...$userIds])
                    ->orWhere(function ($query) use ($currentUserId, $userIds) {
                        $query->where('applied_by_user_id', $currentUserId)
                            ->whereIn('applied_to_user_id', $userIds);
                    })
                    ->orWhere(function ($query) use ($currentUserId, $userIds) {
                        $query->where('applied_to_user_id', $currentUserId)
                            ->whereIn('applied_by_user_id', $userIds);
                    });
            })
            ->get();

        return $applications;
    }
}
