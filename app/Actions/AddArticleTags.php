<?php


namespace App\Actions;


use App\Models\Article;
use Illuminate\Support\Facades\DB;

/**
 * Class AddArticleTags
 * @package App\Actions
 */
class AddArticleTags
{
    /**
     * @param Article $article
     * @param array $data
     * @param bool $delExisting
     */
    public static function execute(Article $article, array $data, bool $delExisting = false)
    {
        if (count($data) == 0) return;
        $data = array_map(fn($value) => ['name' => $value], array_unique($data));
        if (count($data)) {
            DB::transaction(function () use ($delExisting, $article, $data) {
                if ($delExisting) $article->tags()->delete();
                $article->tags()->createMany($data);
            });
        }
    }
}
