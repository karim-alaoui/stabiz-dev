<?php


namespace App\Actions;


use App\Models\Article;
use App\Traits\RelationshipTrait;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateArticle
 * @package App\Actions
 */
class CreateArticle
{
    /**
     * @param array $data
     * @return Article
     * @throws Exception
     */
    public static function execute(array $data): Article
    {
        DB::beginTransaction();
        $articleData = Arr::only($data, ['title', 'description', 'content', 'publish_after', 'hide_after', 'is_draft']);
        // this will turn is_draft to true or false if the value doesn't exist
        Arr::set($articleData, 'is_draft', (bool)Arr::get($articleData, 'is_draft'));
        $audience = Arr::get($data, 'audience', []);
        /**@var Article $article */
        $article = Article::create($articleData);
        if (gettype($audience) == 'array') StoreArticleAudience::execute($article, $audience);
        $tags = Arr::get($data, 'tags', []);
        if (gettype($tags) == 'array') AddArticleTags::execute($article, $tags);

        // add categories
        AddArticleCat::execute($article, Arr::get($data, 'category_ids', []));

        // Add industries to the article
        AddIndustryToArticle::execute($article, Arr::get($data, 'industry_ids', []));

        DB::commit();
        $article->load(RelationshipTrait::articleRelations());
        return $article;
    }
}
