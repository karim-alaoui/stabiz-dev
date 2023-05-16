<?php

namespace App\Http\Controllers\API;

use App\Actions\DeleteUser;
use App\Actions\GetUsers;
use App\Actions\UpdateUser;
use App\Exceptions\ActionException;
use App\Exceptions\ActionValidationException;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\GetUsersByStaffReq;
use App\Http\Requests\UpdateEntrUserReq;
use App\Http\Requests\UpdateFdrReq;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * @group User management
 *
 * User management by the staff.
 * Only super admin can access this apart from the show endpoint
 */
class UserMgmt extends BaseApiController
{
    /**
     * Get Users
     *
     * @param GetUsersByStaffReq $request
     * @return AnonymousResourceCollection
     * @throws ActionException
     * @responseFile storage/responses/entrs.json
     */
    public function index(GetUsersByStaffReq $request): AnonymousResourceCollection
    {
        Gate::authorize('');
        $users = GetUsers::execute(explode(',', $request->type), $request->all());
        return UserResource::collection($users);
    }

    /**
     * Get user
     *
     * Get all details of an user. Can be accessed by both staff and user
     * @param int $id
     * @return UserResource
     */
    public function show(int $id): UserResource
    {
        /**@var User $user */
        $user = User::findOrFail($id);
        $user->load('income');
        if ($user->type == User::FOUNDER) $user->load('fdrProfileWithRelations');
        elseif ($user->type == User::ENTR) $user->load('entrProfileWithRelations');
        return new UserResource($user);
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(int $id): Response
    {
        Gate::authorize('');
        $user = User::findOrFail($id);
        DeleteUser::execute($user);

        return $this->noContent();
    }

    /**
     * Update entrepreneur user
     *
     * Almost the same as updating entrepreneur from the entrepreneur's side
     * @param UpdateEntrUserReq $request
     * @param User $user
     * @return UserResource
     * @throws ActionValidationException
     */
    public function updateEntr(UpdateEntrUserReq $request, User $user): UserResource
    {
//        Gate::authorize('');
        $user = UpdateUser::execute($user, $request->validated());
        return new UserResource($user);
    }

    /**
     * Update founder user
     *
     * Almost the same as updating founder from the founder's side
     * @param UpdateFdrReq $request
     * @param User $user
     * @return UserResource
     * @throws ActionValidationException
     */
    public function updateFdr(UpdateFdrReq $request, User $user): UserResource
    {
//        Gate::authorize('');
        UpdateUser::execute($user, $request->validated());
        return new UserResource($user);
    }
}
