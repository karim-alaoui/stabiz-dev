<?php


namespace App\Actions;


use App\Models\EmailTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Class GetTemplates
 * @package App\Actions
 */
class GetTemplates
{
    /**
     * @param array $query
     * @return LengthAwarePaginator
     */
    public static function execute(array $query): LengthAwarePaginator
    {
        $where = [];
        $id = Arr::get($query, 'id');
        if ($id) $where[] = ['id', $id];

        $subject = Arr::get($query, 'subject');
        if ($subject) $where[] = ['subject', 'ilike', "%$subject%"];

        $body = Arr::get($query, 'body');
        if ($body) $where[] = ['body', 'ilike', "%$body%"];

        return EmailTemplate::query()->where($where)->paginate(
            perPage: Arr::get($query, 'per_page', 15),
            page: Arr::get($query, 'page', 1)
        );
    }
}
