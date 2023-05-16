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
use App\Traits\RelationshipTrait;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function getEntrepreneursList(Request $request)
    {
        $user = auth()->user();

        if ($user->type !== User::FOUNDER) {
            return response()->json(['message' => 'User is not a founder'], 403);
        }

        $entrepreneurs = User::where('type', User::ENTR)
            ->with(['entrProfile' => function ($query) {
                $query->select('id', 'user_id', 'area_id', 'present_post_id');
            }, 'entrProfile.area:id,name_ja', 'entrProfile.industriesPfd:id,name', 'entrProfile.positionsPfd:id,name'])
            ->join('income_ranges', 'users.income_range_id', '=', 'income_ranges.id')
            ->select('users.id', 'users.first_name', 'income_ranges.upper_limit', DB::raw("date_part('year',age(users.dob)) AS age"))
            ->get();

        return response()->json($entrepreneurs);
    }
    public function getFoundersList(Request $request)
    {
        $user = auth()->user();

        if ($user->type !== User::ENTR) {
            return response()->json(['message' => 'User is not an entrepreneur'], 403);
        }

        $founders = User::where('type', User::FOUNDER)
            ->with('fdrProfile')
            ->get();
    
        return $founders;
    }
    
}
