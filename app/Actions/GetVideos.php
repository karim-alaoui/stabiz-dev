<?php


namespace App\Actions;


use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Class GetVideos
 * @package App\Actions
 */
class GetVideos
{
    /**
     * @param array $query
     * @return LengthAwarePaginator
     */
    public static function execute(array $query): LengthAwarePaginator
    {
        $where = [];
        $title = Arr::get($query, 'title');
        if ($title) $where[] = ['title', 'ilike', "%$title%"];

        $id = Arr::get($query, 'id');
        if ($id) $where[] = ['id', $id];

        $description = Arr::get($query, 'description');
        if ($description) $where[] = ['description', 'ilike', $description];

        return Video::query()
            ->where($where)
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }
}
