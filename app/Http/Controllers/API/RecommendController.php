<?php

namespace App\Http\Controllers\API;

use App\Actions\RecLst4User;
use App\Actions\RecommendFn;
use App\Actions\RecUserSearch;
use App\Exceptions\ActionException;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\RcmdListReq;
use App\Http\Requests\RecList4UserReq;
use App\Http\Requests\RecommendReq;
use App\Http\Resources\RecommendationResource;
use App\Http\Resources\UserResource;
use App\Models\Recommendation;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @group Recommendation
 *
 * Recommend entrs to fdr and fdr to entrs
 */
class RecommendController extends BaseApiController
{
    /**
     * Recommendation list search
     *
     * Search users that the staff would recommend
     * @param RcmdListReq $request
     * @return AnonymousResourceCollection
     * @throws ActionException
     */
    public function recUserSearch(RcmdListReq $request): AnonymousResourceCollection
    {
        $users = RecUserSearch::execute(User::find($request->user_id), $request->all());
        return UserResource::collection($users);
    }

    /**
     * Recommend
     *
     * The staff manually recommend one user to another by searching for it.
     * Can be only accessed by staff
     * @param RecommendReq $request
     * @return JsonResponse
     * @throws ActionException
     */
    public function recommend(RecommendReq $request): JsonResponse
    {
        $recommendToUser = User::find($request->recommended_to_user_id);
        $recommendUser = User::find($request->recommended_user_id);
        /**@var Staff $staff */
        $staff = auth()->user();
        RecommendFn::execute($staff, $recommendUser, $recommendToUser);

        return $this->successMsg(__('Recommended successfully'));
    }

    /**
     * Recommend list for user
     *
     * This will return the list of users recommended to one user
     * Can be accessed by the user side
     * @param RecList4UserReq $request
     * @return AnonymousResourceCollection
     */
    public function recList4User(RecList4UserReq $request): AnonymousResourceCollection
    {
        /**@var User $user */
        $user = auth()->user();
        return RecommendationResource::collection(RecLst4User::execute($user, $request->all()));
    }

    /**
     * Recommended list of an user
     *
     * Get the users recommended to an user. This has to be accessed from the staff side.
     * @urlParam user required user id of the user whose recommended list you want to access No-example
     * @param User $user
     * @param RecList4UserReq $request
     * @return AnonymousResourceCollection
     */
    public function accessRcmdListOfUser(User $user, RecList4UserReq $request): AnonymousResourceCollection
    {
        return RecommendationResource::collection(RecLst4User::execute($user, $request->all()));
    }

    /**
     * Remove recommended user
     *
     * Remove a recommended user from an user's recommended list of users
     * Can be accessed by only staff side.
     * @urlParam user required the id of the user from whose list you want to remove No-example
     * @urlParam removeUser required the id of the user that you want to remove from the list No-example
     * @param User $user
     * @param User $removeUser
     * @return Response
     */
    public function removeFromRcmdLst(User $user, User $removeUser): Response
    {
        Recommendation::query()
            ->where([
                'recommended_to_user_id' => $user->id,
                'recommended_user_id' => $removeUser->id
            ])->delete();

        return $this->noContent();
    }
}
