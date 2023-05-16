<?php

namespace App\Http\Controllers\API;

use App\Actions\SearchEntrs;
use App\Actions\SearchFounder;
use App\Actions\ViewEntr;
use App\Actions\ViewFounder;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\SearchEntrByFdrReq;
use App\Http\Requests\SearchFdrReq;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\SearchFdrCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * @group Search
 *
 * Search by entrepreneurs and founders
 */
class SearchController extends BaseApiController
{
    /**
     * Search Founders
     *
     * This search is done by entrepreneurs looking for founders
     * @throws AuthorizationException
     */
    public function searchFdr(SearchFdrReq $request): SearchFdrCollection
    {
        $this->authorize('searchFdr', User::class);

        $results = SearchFounder::execute($request->all());
        return new SearchFdrCollection($results);
    }


    /**
     * View founder
     *
     * After search result, use this to see the full details of the founder
     * @urlParam $founder required founder user id No-example
     * @param User $founder
     * @return UserResource
     * @throws AuthorizationException
     * @throws Exception
     */
    public function getFdr(User $founder): UserResource
    {
        $this->authorize('viewFounder', $founder);
        return new UserResource(ViewFounder::execute($founder));
    }

    /**
     * View entrepreneur
     *
     * Same as founder result
     * @param User $entrepreneur
     * @return UserResource
     * @throws AuthorizationException
     * @throws Exception
     */
    public function getEntr(User $entrepreneur): UserResource
    {
        $this->authorize('viewEntrepreneur', $entrepreneur);
        return new UserResource(ViewEntr::execute($entrepreneur));
    }

    /**
     * Search entrepreneurs
     *
     * for founders looking for entrepreneurs
     * @param SearchEntrByFdrReq $request
     * @return PaginatedResource
     * @throws AuthorizationException
     */
    public function searchEntr(SearchEntrByFdrReq $request): PaginatedResource
    {
        $this->authorize('searchEntr', User::class);

        return new PaginatedResource(SearchEntrs::execute($request->all()));
    }
}
