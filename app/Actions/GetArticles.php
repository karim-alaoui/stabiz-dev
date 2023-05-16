<?php


namespace App\Actions;


use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Class GetArticles
 * @package App\Actions
 */
class GetArticles
{
    /*public function search(array $query)
    {
        $articles = Article::query();

        $getValue = fn($key) => Arr::get($query, $key);
        $where = [];

        $id = $getValue('id');
        if ($id) $where[] = ['id', $id];

        if ($title = $getValue('title')) $articles = $articles->search($title);

        $content = $getValue('content');
        if ($content) $where[] = ['content', 'ilike', "%$content%"];

        $description = $getValue('description');
        if ($description) $where[] = ['description', 'ilike', "%$description%"];

        $draft = Arr::exists($query, 'draft');
        if ($draft) $where[] = ['is_draft', 'ilike', db_bool_val(Arr::get($query, 'draft'))];

        $publishAfter = Arr::get($query, 'publish_after_gte');
        if ($publishAfter) $where[] = ['publish_after_gte', '>=', $publishAfter];

        $hideAfter = Arr::get($query, 'hide_after_gte');
        if ($hideAfter) $where[] = ['hide_after_gte', '>=', $hideAfter];

        $audience = Arr::get($query, 'audience');
        if ($audience) {
            $articles = $articles->whereHas('audiences', function (Builder $query) use ($audience) {
                // audience would a string here seperated by comma in case both values are provided
                $query->whereIn('audience', explode(',', $audience));
            });
        }

        return $articles->where($where)
            ->with('audiences')
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
    }*/

    /**
     * @param array $query
     * @return LengthAwarePaginator
     */
    public static function execute(array $query): LengthAwarePaginator
    {
        $searchQ = Arr::get($query, 'query');
        $articles = Article::search($searchQ, function ($meilisearch, string|null $meilisearchQ, array $options) use ($query) {
            $options['sort'] = ['id:desc'];
            return $meilisearch->search($meilisearchQ, $options);
        });

        // convert this value to string as there's no boolean  on meilisearch only string and number
        $draft = bool_convert(Arr::get($query, 'draft')) ? 'true' : 'false';
        if (Arr::exists($query, 'draft')) $articles = $articles->where('is_draft', $draft);

        $articles = $articles
            ->paginate(
                perPage: Arr::get($query, 'per_page', 15),
                page: Arr::get($query, 'page', 1)
            );
        $articles->load(['audiences', 'tags']);
        return $articles;
    }
}
