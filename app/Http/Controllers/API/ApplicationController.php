<?php

namespace App\Http\Controllers\API;

use App\Actions\AcceptApl;
use App\Actions\Apply;
use App\Actions\GetAppliedApl;
use App\Actions\GetRecvdApl;
use App\Exceptions\ActionException;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\ApplyReq;
use App\Http\Requests\FilterAplReq;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\UserResource;
use App\Models\Application;
use App\Models\User;
use App\Traits\RelationshipTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

/**
 * @group Application
 *
 * Endpoints for applying to entrepreneurs/founders
 */
class ApplicationController extends BaseApiController
{
    /**
     * Apply to founder/entrepreneur
     *
     * Founder can only apply to entrepreneur and entr to fdr.
     * Applying to same type of user will result in error.
     * @param ApplyReq $request
     * @return JsonResponse
     * @throws ActionException
     */
    public function apply(ApplyReq $request): JsonResponse
    {
        /**@var User $user */
        $user = auth()->user();

        Apply::execute($user, User::findOrFail($request->apply_to_user_id));
        return $this->successMsg(code: 201);
    }

    /**
     * Reject application
     *
     * @urlParam application required application id No-example
     * @param Application $application
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function reject(Application $application): JsonResponse
    {
        $this->authorize('acceptApplication', $application);
        if (!is_null($application->rejected_at)) return $this->errorMsg(__('Application already rejected'));
        $application->rejected_at = now();
        $application->accepted_at = null;
        $application->save();
        return $this->successMsg(__('Rejected application'));
    }

    /**
     * Accept application
     *
     * @urlParam application required application id No-example
     * @param Application $application
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ActionException
     */
    public function accept(Application $application): JsonResponse
    {
        $this->authorize('acceptApplication', $application);
        AcceptApl::execute($application);
        return $this->successMsg(__('Accepted application'));
    }

    /**
     * Applied applications
     *
     * created_at is applied_at time
     * @queryParam page current page number for pagination Example: 2
     * @queryParam per_page the number of results to return per request. Default value is 15 Example: 15
     * @param FilterAplReq $request
     * @return PaginatedResource
     */
    public function applied(FilterAplReq $request): PaginatedResource
    {
        return new PaginatedResource(GetAppliedApl::execute(
            auth()->user(),
            $request->all()
        ));
    }

    /**
     * Check if applied
     *
     * Check if the auth user has applied to this user or not
     * @urlParam appliedToUserId required the id of the user to whom the user applied to No-example
     * @param int $appliedToUserId
     * @return JsonResponse
     */
    public function checkIfApplied(int $appliedToUserId): JsonResponse
    {
        $application = Application::query()
            ->where([
                'applied_by_user_id' => auth()->id(),
                'applied_to_user_id' => $appliedToUserId
            ])
            ->select(['id'])
            ->first();
        return $this->success([
            'applied' => (bool)$application
        ]);
    }

    /**
     * Received applications
     *
     * This will return all the applications by default (both rejected and accepted. You can always filter by `type` query)
     * @param FilterAplReq $request
     * @return PaginatedResource
     */
    public function recvdApl(FilterAplReq $request): PaginatedResource
    {
        return new PaginatedResource(
            GetRecvdApl::execute(auth()->user(), $request->all())
        );
    }

    /**
     * Applicant details
     *
     * Get user details of an applicant
     * If the user is founder, the user will have founder_profile
     * otherwise the user will have entr_profile key
     * @param Application $application
     * @return UserResource
     * @throws AuthorizationException
     * TODO: write tests for this
     */
    public function applicantDetails(Application $application): UserResource
    {
        $this->authorize('getApplicantDetails', $application);
        /**@var User $user */
        $user = $application->applied_by_user_id;
        if ($user->type == User::ENTR) $user->load(RelationshipTrait::entrProfileRelations());
        else $user->load(RelationshipTrait::fdrProfileRelations());

        return new UserResource($user);
    }
}
