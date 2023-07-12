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

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FounderUser;
use App\Models\FounderProfile;
use App\Models\Organizer;
use Illuminate\Support\Facades\Auth;

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
    public function getFounderUsersByFounderProfile(Request $request, $id)
    {
        // Get the FounderProfile by its ID
        $founderProfile = FounderProfile::find($id);
        if (!$founderProfile) {
            return response()->json(['message' => 'Founder profile not found'], 404);
        }

        // Get the FounderUsers belonging to this FounderProfile
        $founderUsers = FounderUser::where('founder_id', $founderProfile->id)->get();

        // Get the User details for each FounderUser
        $founderUsersDetails = [];

        foreach ($founderUsers as $founderUser) {
            $user = User::find($founderUser->user_id);

            $founderUsersDetails[] = [
                'id' => $user->id,
                'role' => $founderUser->role,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ];
        }
        // Return the FounderUsers details
        return response()->json(['founder_users' => $founderUsersDetails]);
    }
    public function createFounderUser(Request $request)
    {
        $founderId = $request->input('founder_id');
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $password = $request->input('password');
        $role = $request->input('role');
    
        // Check if the user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return response()->json(['message' => 'User already exists'], 400);
        }

        // Create a new user
        try {
            $user = new User();
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->email = $email;
            $user->type = 'founder';
            $user->password = Hash::make($password);
            $user->save();
        }catch (\Throwable $th) {
            // Handle any errors with creating the user
        }
        // Store the founder user ID, founder ID, and role of founder user in the new table
        $founderUser = new FounderUser();
        $founderUser->founder_id = $founderId;
        $founderUser->user_id = $user->id;
        $founderUser->role = $role;
        $founderUser->save();
    
        // Return a response indicating that the founder user was created successfully
        return response()->json(['message' => 'Founder user created successfully']);
    }
    public function getFounderUserDetails($userId)
    {
        $founderUser = FounderUser::where('user_id', $userId)->first();

        if (!$founderUser) {
            return response()->json(['message' => 'Founder user not found'], 404);
        }

        $user = $founderUser->user;

        return response()->json([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => $founderUser->role,
        ]);
    }
    public function updateFounderUser(Request $request, $userId)
    {
        // Find the founder user by ID
        $founderUser = FounderUser::where('user_id', $userId)->first();
    
        // Check if the founder user exists
        if (!$founderUser) {
            return response()->json(['message' => 'Founder user not found'], 404);
        }
    
        // Fill the founder user with the request data
        $founderUser->fill($request->all());
        $founderUser->save();

        $user = User::find($founderUser->user_id);
        if ($user) {
            $user->fill($request->only(['first_name', 'last_name', 'email']));
            $password = $request->input('password');
            if ($password) {
                $user->password = Hash::make($password);
            }
            $user->save();
        }
        // Return a response indicating that the founder user was updated successfully
        return response()->json(['message' => 'Founder user updated successfully']);
    }
    public function getOrganizers(Request $request)
    {
        $organizers = Organizer::query();

        return $organizers->paginate(
            perPage: Arr::get($request->all(), 'per_page', 15),
            page: Arr::get($request->all(), 'page', 1)
        );
    }
    public function indexOrganizerProfile(Request $request, $userId)
    {
        $organizer = Organizer::findOrFail($userId);

        return response()->json($organizer);
    }
    public function getOrganizerFounderProfiles(Request $request, $userId) 
    {
        $organizerId = $userId;

        $founderProfiles = FounderProfile::where('user_id', $organizerId)
            ->with(['area', 'prefecture', 'offeredIncome'])
            ->get();
    
        return response()->json($founderProfiles);
    }
}
