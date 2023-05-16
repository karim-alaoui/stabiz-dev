<?php


namespace App\Actions;


use App\Models\Article;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class StoreArticleAudience
 * @package App\Actions
 */
class StoreArticleAudience
{
    /**
     * @throws Exception
     */
    public static function execute(Article $article, string|array $audience)
    {
        $valid = ['founder', 'entrepreneur'];
        if (gettype($audience) == 'string') {
            if (!in_array($audience, $valid)) {
                throw new Exception(__('Invalid audience provided'));
            }
            $audiences = [$audience];
        } else {
            array_map(function ($val) use ($valid) {
                if (!in_array($val, $valid)) {
                    throw new Exception(__('Invalid audience ' . $valid . ' provided'));
                }
            }, $audience);

            $audiences = $audience;
        }

        if (count($audiences) == 0) return;

        $audiences = array_map(fn($val) => ['audience' => $val], array_unique($audiences));

        if (count($audiences)) {
            DB::transaction(function () use ($audiences, $article) {
                $article->audiences()->delete();
                $article->audiences()->createMany($audiences);
            });
        }
    }
}
