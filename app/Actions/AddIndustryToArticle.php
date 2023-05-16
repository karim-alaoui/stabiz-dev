<?php

namespace App\Actions;

use App\Models\Article;
use Illuminate\Support\Facades\DB;

class AddIndustryToArticle
{
    public static function execute(Article $article, array $industryIds)
    {
        if (count($industryIds) == 0) return;
        DB::transaction(function () use ($article, $industryIds) {
            $industries = array_map(fn($industry) => ['industry_id' => $industry], array_unique($industryIds));
            $article->industries()->delete();
            $article->industries()->createMany($industries);
        });
    }
}
