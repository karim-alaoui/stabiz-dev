<?php

namespace App\Http\Controllers\API;

use App\Actions\GetStaff;
use App\Actions\LogoutEverywhere;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\CreateStaffReq;
use App\Http\Requests\GetStaffReq;
use App\Http\Requests\UpdateStaffPassReq;
use App\Http\Requests\UpdateStaffReq;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

/**
 * @group Staff
 */
class StaffController extends BaseApiController
{
    /**
     * Get auth staff
     *
     * Get current auth staff
     * @responseFile storage/responses/staff.json
     */
    public function staff(): StaffResource
    {
        return new StaffResource(auth()->user()->load(['roles']));
    }

    /**
     * Get staff
     *
     * get list of staff
     * @param GetStaffReq $request
     * @return AnonymousResourceCollection
     */
    public function index(GetStaffReq $request): AnonymousResourceCollection
    {
        Gate::authorize('');
        $staff = GetStaff::execute($request->all());
        return StaffResource::collection($staff);
    }


    /**
     * Create staff
     *
     * @param CreateStaffReq $request
     * @return mixed
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     */
    public function store(CreateStaffReq $request): mixed
    {
        Gate::authorize('');
        $data = $request->validated();
        Arr::pull($data, 'confirm_password');
        $role = Arr::pull($data, 'role');
        Arr::set($data, 'password', Hash::make(Arr::get($data, 'password')));
        $staff = Staff::create($data);
        $staff->assignRole(strtolower($role));

        return (new StaffResource($staff))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update staff password
     *
     * @urlParam staff required staff id No-example
     * @param UpdateStaffPassReq $request
     * @param Staff $staff
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function updatePass(UpdateStaffPassReq $request, Staff $staff): JsonResponse
    {
        $this->authorize('updatePassword', $staff);

        DB::transaction(function () use ($staff, $request) {
            $staff->password = Hash::make($request->password);
            $staff->save();
            LogoutEverywhere::execute($staff);
        });

        return $this->successMsg();
    }

    /**
     * Update staff
     *
     * @urlParam staff required staff id No-example
     * @param UpdateStaffReq $request
     * @param Staff $staff
     * @return StaffResource
     */
    public function update(UpdateStaffReq $request, Staff $staff): StaffResource
    {
        Gate::authorize('');
        $data = $request->validated();
        $role = Arr::pull($data, 'role');
        DB::transaction(function () use ($staff, $role, $data) {
            Staff::where('id', $staff->id)
                ->take(1)
                ->update($data);
            if ($role) {
                $staff->roles()->detach();
                $staff->assignRole($role);
            }
        });
        return new StaffResource($staff->refresh());
    }

    /**
     * Delete staff
     *
     * @urlParam staff required staff Id No-example
     * @param Staff $staff
     * @return Response
     */
    public function destroy(Staff $staff): Response
    {
        Gate::authorize('');
        $staff->delete();
        return $this->noContent();
    }
}
