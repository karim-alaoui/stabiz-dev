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
use App\Http\Resources\FounderProfileResource;
use App\Models\Application;
use App\Models\User;
use App\Models\FounderProfile;
use App\Traits\RelationshipTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $user = auth()->user();
        $userId = auth()->id();

        if($user->type == "founder"){
            $founderProfile = FounderProfile::join('founder_user', 'founder_profiles.id', '=', 'founder_user.founder_id')
            ->where('founder_user.user_id', $user->id)
            ->first();

            if (!$founderProfile) {
                return response()->json(['message' => 'Founder profile not found'], 404);
            }else{
                $userId = $founderProfile->id;
            }
        }
        
        $application = Application::query()
            ->where([
                'applied_by_user_id' => $userId,
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
    public function getAllApplications(){

        $applications = Application::whereNotNull('accepted_at')->get();

        $responseData = [];

        foreach ($applications as $application) {

            $appliedToUserId = $application->applied_to_user_id;
            $appliedByUserId = $application->applied_by_user_id;

            if ($appliedToUserId >= 100000) {

                $entrepreneur = User::find($appliedToUserId);
                $entrepreneur->load('income');
                if ($entrepreneur->type == User::ENTR) $entrepreneur->load('entrProfileWithRelations');
                $entrepreneur = new UserResource($entrepreneur);

                $founderProfile = FounderProfile::find($appliedByUserId);
                //$founderProfile = new FounderProfileResource(FounderProfile::find($appliedByUserId));

            } elseif ($appliedByUserId >= 100000) {

                $entrepreneur = User::find($appliedByUserId);
                $entrepreneur->load('income');
                if ($entrepreneur->type == User::ENTR) $entrepreneur->load('entrProfileWithRelations');
                $entrepreneur = new UserResource($entrepreneur);

                $founderProfile = FounderProfile::find($appliedToUserId);
                //$founderProfile = new FounderProfileResource(FounderProfile::find($appliedToUserId));
            } else {
                return response()->json(['message' => 'Error, something went wrong (couldnt find an entrepreneur with id more than 100000'], 500); 
            }

            $responseData[] = [
                'application' => $application,
                'entrepreneur' => $entrepreneur,
                'founder' => $founderProfile,
            ];
        }

        return response()->json($responseData);
    }
    public function agreeToNDA(Request $request, $id)
    {
        // Get the authenticated user
        $user = auth()->user();
        $userId = null;

        if ($user->type == User::FOUNDER) {
            $founderProfile = FounderProfile::join('founder_user', 'founder_profiles.id', '=', 'founder_user.founder_id')
            ->where('founder_user.user_id', $user->id)
            ->first();

            if (!$founderProfile) {
                return response()->json(['message' => 'Founder profile not found'], 404);
            } else {
                $userId = $founderProfile->id;
            }

        } elseif ($user->type == User::ENTR) {
            $userId = $user->id;
        } else {
            return response()->json(['message' => 'User is not founder nor entrepreneur'], 403);
        }

        // Find the application by ID
        $application = Application::findOrFail($id);

        // Check if the authenticated user is the owner of the application
        if ($userId !== $application->applied_to_user_id && $userId !== $application->applied_by_user_id ) {
            return response()->json(['message' => 'You are not authorized to agree to the NDA for this application'], 403);
        }

        // Update the application's founder_NDA column to the current time
        if($user->type == User::FOUNDER){
            $application->update(['founder_NDA' => now()]);
        } elseif ($user->type == User::ENTR){
            $application->update(['entrepreneur_NDA' => now()]);
        }
        
        return response()->json(['message' => 'NDA agreed successfully']);
    }
    public function update(Request $request, $id)
    {
        // Retrieve the application based on the provided ID
        $application = Application::findOrFail($id);
        
        // Validate the request data
        $validatedData = $request->validate([
            'negotiations' => 'nullable',
            'admin' => 'nullable|exists:staff,id',
        ]);
        
        // Update the negotiation and admin columns
        if (isset($validatedData['negotiations'])) {
            $application->negotiations = $validatedData['negotiations'];
        }
        
        if (isset($validatedData['admin'])) {
            $application->admin = $validatedData['admin'];
        }
        
        // Save the changes
        $application->save();
        
        return response()->json(['message' => 'Application updated successfully']);
    }
}
