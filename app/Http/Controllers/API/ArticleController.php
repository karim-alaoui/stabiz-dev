<?php

namespace App\Http\Controllers\API;

use App\Actions\CreateArticle;
use App\Actions\GetArticles;
use App\Actions\UpdateArticle;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\GetArticleReq;
use App\Http\Requests\StoreArticleReq;
use App\Http\Requests\UpdateArticleReq;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Traits\RelationshipTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @group Article
 *
 * CRUD method for articles
 */
class ArticleController extends BaseApiController
{
    public function __construct()
    {
        $this->authorizeResource(Article::class);
    }

    /**
     * Get articles
     *
     * @param GetArticleReq $request
     * @return AnonymousResourceCollection
     */
    public function index(GetArticleReq $request): AnonymousResourceCollection
    {
        return ArticleResource::collection(GetArticles::execute($request->all()));
    }

    /**
     * Create an article
     *
     * You can attach multiple categories for an article. Keep that in mind.
     * @param StoreArticleReq $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StoreArticleReq $request): JsonResponse
    {
        Gate::authorize('');
        $article = CreateArticle::execute($request->all());
        return (new ArticleResource($article))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get an article
     *
     * @urlParam article required article id No-example
     * @param $article
     * @return ArticleResource
     * @throws Exception
     */
    public function show($article): ArticleResource
    {
        $cacheKey = Article::cacheKey($article);
        $article = cache()->remember($cacheKey, now()->addWeek(), function () use ($article) {
            $article = Article::findOrFail($article);
            $article->load(RelationshipTrait::articleRelations());
            return $article;
        });
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateArticleReq $request
     * @param Article $article
     * @return ArticleResource
     */
    public function update(UpdateArticleReq $request, Article $article): ArticleResource
    {
        $article = UpdateArticle::execute($article, $request->all());
        $article->load(RelationshipTrait::articleRelations());

        return new ArticleResource($article);
    }

    /**
     * Delete an article
     *
     * @param Article $article
     * @return Response
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function destroy(Article $article): Response
    {
        cache()->delete($article::cacheKey($article->id));
        $article->delete();
        return $this->noContent();
    }
}
