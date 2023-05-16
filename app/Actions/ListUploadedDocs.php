<?php

namespace App\Actions;

use App\Models\UploadedDoc;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ListUploadedDocs
{
    public static function execute(array $query): LengthAwarePaginator
    {
        $docsQuery = UploadedDoc::query();
        $state = Arr::get($query, 'state', '');
        $state = trim(strtolower($state));

        $docsQuery = match ($state) {
            'approved' => $docsQuery->approved(),
            'rejected' => $docsQuery->rejected(),
            'not touched' => $docsQuery->notTouched(),
            default => $docsQuery
        };

        $userId = Arr::get($query, 'user_id');
        if ($userId) $docsQuery = $docsQuery->where('user_id', $userId);

        // sort the documents in a way that the ones which were uploaded first
        // but were not approved and not rejected either. Basically,  there are untouched.
        return $docsQuery->orderByRaw('approved_at nulls first, rejected_at nulls first, created_at')
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
