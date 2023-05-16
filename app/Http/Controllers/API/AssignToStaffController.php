<?php

namespace App\Http\Controllers\API;

use App\Actions\AssignUsers;
use App\Actions\GetAssignedUser;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\AssignUserReq;
use App\Http\Requests\GetAsgnUserReq;
use App\Http\Resources\AssignedUserResource;
use App\Http\Resources\AssignUserCollection;
use App\Models\AssignedUserToStaff;
use App\Models\Staff;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * @group Assign to staff
 *
 * Assign entrepreneurs and founders to staff
 */
class AssignToStaffController extends BaseApiController
{
    /**
     * Get assigned users
     *
     * @param GetAsgnUserReq $request
     * @return AssignUserCollection
     */
    public function index(GetAsgnUserReq $request): AssignUserCollection
    {
        /**@var Staff $staff */
        $staff = auth()->user();
        $assigned = GetAssignedUser::execute($staff, $request->all());
        return new AssignUserCollection($assigned);
    }

    /**
     * Assign users
     *
     * @param AssignUserReq $request
     * @return JsonResponse
     */
    public function store(AssignUserReq $request): JsonResponse
    {
        Gate::authorize('');
        AssignUsers::execute(Staff::findOrFail($request->staff_id), $request->user_id);
        return $this->successMsg(__('Added successfully'), 201);
    }

    /**
     * Get assigned user
     *
     * It will load all the details of the staff and the user
     * @url assign_user required id of the assigned. neither `user id` nor `staff id` apart from these two, the other id that you see No-example
     * @param int $id
     * @return AssignedUserResource
     * @throws AuthorizationException
     */
    public function show(int $id): AssignedUserResource
    {
        /**@var AssignedUserToStaff $assigned */
        $assigned = AssignedUserToStaff::findOrFail($id);
        $this->authorize('show', $assigned);

        $assigned->load([
            'staff',
            'user.entrProfileWithRelations',
            'user.fdrProfileWithRelations'
        ]);

        return new AssignedUserResource($assigned);
    }

    /**
     * Remove assigned user
     *
     * @param $id
     * @return Response
     */
    public function destroy($id): Response
    {
        Gate::authorize('');
        $assigned = AssignedUserToStaff::findOrFail($id);
        $assigned->delete();
        return $this->noContent();
    }
}
