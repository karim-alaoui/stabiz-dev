<?php

namespace App\Actions;

use App\Models\Article;
use App\Models\Category4Article;
use Illuminate\Support\Facades\DB;

/**
 * Add categories to an article
 */
class AddArticleCat
{
    public static function execute(Article $article, array $categoryIds)
    {
        if (count($categoryIds) === 0) return;

        // this will filter the category ids and will keep
        // only those category ids which actually exist in our database
        $categoryIds = Category4Article::query()
            ->whereIn('id', $categoryIds)
            ->get()
            ->pluck('id')
            ->toArray();

        if (count($categoryIds) === 0) return;

        DB::transaction(function () use ($article, $categoryIds) {
            $article->categories()->delete();
            $categories = array_map(fn($value) => ['category_id' => $value], array_unique($categoryIds));
            $article->categories()->createMany($categories);
        });
    }
}
