<?php


namespace App\Actions;


use App\Models\Article;
use App\Traits\RelationshipTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateArticle
 * @package App\Actions
 */
class UpdateArticle
{
    /**
     * @param Article $article
     * @param array $data
     * @return Article
     */
    public static function execute(Article $article, array $data): Article
    {
        $update = [
            'title',
            'description',
            'content',
            'publish_after',
            'hide_after',
        ];

        array_map(function ($column) use (&$article, $data) {
            if (Arr::exists($data, $column)) {
                $article->{$column} = Arr::get($data, $column);
            }
        }, $update);

        if (Arr::exists($data, 'is_draft')) {
            $article->is_draft = db_bool_val(Arr::get($data, 'is_draft'));
        }

        DB::transaction(function () use ($data, $article) {
            $article->save();
            $audiences = Arr::get($data, 'audience', []);
            $tags = Arr::get($data, 'tags', []);
            if (gettype($audiences) == 'array' && count($audiences)) StoreArticleAudience::execute($article, $audiences);
            if (gettype($tags) == 'array' && count($tags)) AddArticleTags::execute($article, $tags);
            AddArticleCat::execute($article, Arr::get($data, 'category_ids', []));
            AddIndustryToArticle::execute($article, Arr::get($data, 'industry_ids', []));
        });
        $article->load(RelationshipTrait::articleRelations());
        return $article;
    }
}
