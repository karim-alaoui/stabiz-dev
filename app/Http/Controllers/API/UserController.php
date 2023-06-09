<?php

namespace App\Http\Controllers\API;

use App\Actions\ChangeUserPassword;
use App\Actions\CheckPassword;
use App\Actions\DeleteDP;
use App\Actions\DeleteUser;
use App\Actions\UpdateCompanyImgs;
use App\Actions\UpdateDP;
use App\Actions\UpdateUser;
use App\Exceptions\ActionException;
use App\Exceptions\ActionValidationException;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\DeleteUserReq;
use App\Http\Requests\UpdateComImgReq;
use App\Http\Requests\UpdateEntrUserReq;
use App\Http\Requests\UpdateFdrReq;
use App\Http\Requests\UpdatePassUserReq;
use App\Http\Requests\UploadDPReq;
use App\Http\Resources\FounderProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Area;
use App\Models\Industry;
use App\Models\FounderProfile;
use App\Models\Application;
use App\Traits\RelationshipTrait;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Actions\ApplicationStatus;

/**
 * @group User
 *
 * User related actions like update, password change etc
 */
class UserController extends BaseApiController
{
    /**
     * Get user
     *
     * Get logged in user
     * @responseFile storage/responses/user.json
     */
    public function getUser(): UserResource
    {
        /**@var User $user */
        $user = auth()->user();
        $user->load(User::FOUNDER == $user->type ? 'fdrProfileWithRelations' : 'entrProfileWithRelations');
        return new UserResource($user);
    }

    /**
     * Update entrepreneur user
     *
     * ** Note- The bodyParams are not required and all of them are optional.
     * @responseFile storage/responses/update_entr.json
     * @param UpdateEntrUserReq $request
     * @return UserResource|JsonResponse
     * @throws ActionValidationException
     * @throws Exception
     */
    public function updateEntr(UpdateEntrUserReq $request): UserResource|JsonResponse
    {
        $this->authorize('updateEntr', User::class);
        $user = auth()->user();
        if ($user->type !== User::ENTR) throw new AuthorizationException();
        $user = UpdateUser::execute($user, $request->validated());
        $user->entrProfile->load(RelationshipTrait::entrProfileRelations());
        return new UserResource($user);
    }

    /**
     * Update founder
     *
     * @param UpdateFdrReq $request
     * @return UserResource
     * @throws ActionValidationException
     * @throws AuthorizationException
     */
    public function updateFdr(UpdateFdrReq $request): UserResource
    {
        $this->authorize('updateFdr', User::class);
        /**@var User $user */
        $user = auth()->user();
        UpdateUser::execute($user, $request->validated());
        $user->fdrProfile->load(RelationshipTrait::fdrProfileRelations());
        return new UserResource($user);
    }

    /**
     * @param UpdateComImgReq $request
     * @return FounderProfileResource
     * @throws ActionException
     */
    public function updateCompanyImgs(UpdateComImgReq $request): FounderProfileResource
    {
        /**@var User $user */
        $user = \auth()->user();

        $fdrProfile = UpdateCompanyImgs::removeBannerImg((bool)$request->remove_banner)
            ::uploadBanner($request->banner)
            ::removeLogoImg((bool)$request->remove_logo)
            ::uploadLogo($request->logo)
            ::execute($user);

        return new FounderProfileResource($fdrProfile);
    }

    /**
     * Delete account
     *
     * After delete the email address can be used to create an account again
     * @param DeleteUserReq $request
     * @return JsonResponse|Response
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function delete(DeleteUserReq $request): Response|JsonResponse
    {
        /**@var User $user */
        $user = Auth::user();
        $check = CheckPassword::execute($user, $request->password);
        if (!$check) return $this->errorMsg(__('Password do not match'), 401);
        DeleteUser::execute($user);

        return $this->noContent();
    }

    /**
     * Update password
     *
     * @param UpdatePassUserReq $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws ActionException
     */
    public function updatePass(UpdatePassUserReq $request): JsonResponse
    {
        /**@var User $user */
        $user = \auth()->user();
        ChangeUserPassword::execute(
            $user,
            $request->current_password,
            $request->confirm_password,
            $request->boolean('logout_everywhere')
        );

        return $this->successMsg(__('Password updated'));
    }

    /**
     * Update DP
     *
     * Update display picture
     * @param UploadDPReq $request
     * @return UserResource
     */
    public function updateDP(UploadDPReq $request): UserResource
    {
        /**@var User $user */
        $user = \auth()->user();

        $photo = $request->file('photo');
        if ($photo) {
            /**@var User $user */
            $user = UpdateDP::execute($user, $photo); // returns the user instance
        } elseif ($request->has('photo') && is_null($photo)) { // null value provided. So, delete in that case
            $user = DeleteDP::execute($user);
        }

        return new UserResource($user);
    }
    public function getEntrepreneursList(Request $request, ApplicationStatus $applicationStatus)
    {
        $user = auth()->user();

        if ($user->type !== User::FOUNDER) {
            return response()->json(['message' => 'User is not a founder'], 403);
        }

        $founderProfile = FounderProfile::join('founder_user', 'founder_profiles.id', '=', 'founder_user.founder_id')
            ->where('founder_user.user_id', $user->id)
            ->first();

        if (!$founderProfile) {
            return response()->json(['message' => 'Founder profile not found'], 404);
        }

        $entrepreneurs = User::where('type', User::ENTR)
            ->with([
                'income',
                'entrProfile' => function ($query) {
                    $query->select('id', 'user_id', 'area_id')->with(['area', 'industriesPfd', 'positionsPfd']);
                }
            ])
            ->select('users.id', 'users.first_name', 'income_range_id', DB::raw("date_part('year',age(users.dob)) AS age"))
            ->orderBy('users.created_at', 'desc')
            ->get();

        // Retrieve the IDs of entrepreneurs
        $entrepreneurIds = $entrepreneurs->pluck('id')->toArray();

        // Get the application status for each entrepreneur
        $applicationStatuses = $applicationStatus->processApplications($founderProfile->id, $entrepreneurIds);

        // Add the application status to each entrepreneur
        $entrepreneurs->each(function ($entrepreneur) use ($applicationStatuses) {
            $entrepreneur->application_status = $applicationStatuses
                ->where('other_user_id', $entrepreneur->id)
                ->first();
        });

        return $entrepreneurs;
    }
    public function getFoundersList(Request $request, ApplicationStatus $applicationStatus)
    {
        $user = auth()->user();

        if ($user->type !== User::ENTR) {
            return response()->json(['message' => 'User is not an entrepreneur'], 403);
        }

        $founderProfiles = FounderProfile::select('id', 'company_name', 'no_of_employees', 'is_listed_company', 'area_id','offered_income_range_id')
            ->with(['area', 'industries', 'offeredIncome'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Retrieve the IDs of founder profiles
        $founderProfileIds = $founderProfiles->pluck('id')->toArray();

        // Get the application status for each founder profile
        $applicationStatuses = $applicationStatus->processApplications($user->id, $founderProfileIds);

        $founderProfiles->each(function ($founderProfile) use ($applicationStatuses) {
            $founderProfile->application_status = $applicationStatuses
                ->where('other_user_id', $founderProfile->id)
                ->first();
        });
        
        return $founderProfiles;

    }
    public function getFounderProfileDetails($id)
    {
        $authUser = auth()->user();

        $founderProfile = FounderProfile::find($id);

        if (!$founderProfile) {
            return response()->json(['message' => 'Founder profile not found'], 404);
        }

        // Check if the logged-in user is friends with the founder
        $isFriend = Application::where('accepted_at', '!=', null)
            ->where(function ($query) use ($authUser, $founderProfile) {
                $query->where('applied_to_user_id', $authUser->id)
                    ->where('applied_by_user_id', $founderProfile->id);
            })
            ->orWhere(function ($query) use ($authUser, $founderProfile) {
                $query->where('applied_to_user_id', $founderProfile->id)
                    ->where('applied_by_user_id', $authUser->id);
            })
            ->exists();

        if (!$isFriend) {
            return response()->json(['message' => 'You must be friends with this founder to see his details'], 401);
        }

        return new FounderProfileResource($founderProfile);
    }

    public function getEntrepreneurDetails($id)
    {
        $authUser = auth()->user();
        
        $founderProfile = FounderProfile::join('founder_user', 'founder_profiles.id', '=', 'founder_user.founder_id')
            ->where('founder_user.user_id', $authUser->id)
            ->first();

        if (!$founderProfile) {
            return response()->json(['message' => 'Founder profile not found'], 404);
        }

        $entrepreneur = User::findOrFail($id);

        // Check if the logged-in user is friends with the entrepreneur
        $isFriend = Application::where('accepted_at', '!=', null)
            ->where(function ($query) use ($founderProfile, $entrepreneur) {
                $query->where('applied_to_user_id', $founderProfile->id)
                    ->where('applied_by_user_id', $entrepreneur->id);
            })
            ->orWhere(function ($query) use ($founderProfile, $entrepreneur) {
                $query->where('applied_to_user_id', $entrepreneur->id)
                    ->where('applied_by_user_id', $founderProfile->id);
            })
            ->exists();

        if (!$isFriend) {
            return response()->json(['message' => 'You must be friends with this entrepreneur to see his details'], 401);
        }

        $entrepreneur->load('income');
        if ($entrepreneur->type == User::ENTR) $entrepreneur->load('entrProfileWithRelations');
        return new UserResource($entrepreneur);
    }
}
