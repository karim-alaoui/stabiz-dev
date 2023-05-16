<?php


namespace App\Actions;


use App\Models\NewsTopic;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Class GetNewsTopic
 * @package App\Actions
 */
class GetNewsTopic
{
    /**
     * @param array $query
     * @return LengthAwarePaginator
     */
    public static function execute(array $query): LengthAwarePaginator
    {
        $searchQ = Arr::get($query, 'query');
        return NewsTopic::search($searchQ, function ($meilisearch, string|null $meilisearchQ, array $options) use ($query) {
            $filter = [];
            if ($id = Arr::get($query, 'id')) $filter[] = "id=$id";
            if ($showAfter = Arr::get($query, 'show_after_gte')) {
                $showAfter = Carbon::parse($showAfter)->unix();
                $filter[] = "show_after>=$showAfter";
            }

            if ($hideAfter = Arr::get($query, 'hide_after_gte')) {
                $hideAfter = Carbon::parse($hideAfter)->unix();
                $filter[] = "hide_after>=$hideAfter";
            }

            $options['filter'] = count($filter) ? implode(' AND ', $filter) : null;
            $options['sort'] = ['id:desc'];
            return $meilisearch->search($meilisearchQ, $options);
        })
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
