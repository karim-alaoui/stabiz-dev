<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Actions\UpdateCompanyIndustries;
use App\Actions\UpdateAfflCom;
use App\Actions\UpdateStockHolders;
use App\Actions\UpdateFdrPfdIndustries;
use App\Actions\UpdateFdrPfdPrefecture;
use App\Actions\UpdatePfdPositions;

use Illuminate\Http\Request;
use App\Models\Organizer;
use App\Models\User;
use App\Models\FounderUser;
use App\Models\FounderProfile;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\BaseApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrganizerRegistrationConfirmation;
use App\Actions\GetAuthToken;
use App\Http\Requests\LoginReq;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrganizerController extends BaseApiController
{
    public function registerOrganizer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:organizers,email'],
            'password' => ['required', Password::min(10)],
            'professional_corporation_name' => ['required', 'string'],
            'name_of_person_in_charge' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'square_one_members_id' => ['nullable', 'string'],
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Create a new organizer
            $organizer = new Organizer();
            $organizer->email = $request->email;
            $organizer->password = $request->password;
            $organizer->professional_corporation_name = $request->professional_corporation_name;
            $organizer->name_of_person_in_charge = $request->name_of_person_in_charge;
            $organizer->phone_number = $request->phone_number;
            $organizer->square_one_members_id = $request->square_one_members_id;

            // Generate a confirmation code
            $confirmationCode = mt_rand(100000, 999999);

            // Save the confirmation code in the database
            $organizer->confirmation_code = $confirmationCode;
            $organizer->save();

            // Create an instance of the OrganizerRegistrationConfirmation class
            $confirmation = new OrganizerRegistrationConfirmation($confirmationCode);

            // Send the email
            Mail::to($organizer->email)->send($confirmation);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while registering organizer. Please try again.'], 500);
        }
    
        return response()->json(['message' => 'Organizer registered successfully'], 201);
    }

    public function resendConfirmationCode(Request $request)
    {
        $organizer = Organizer::where('email', $request->input('email'))->first();

        if (!$organizer) {
            return response()->json(['message' => 'Organizer not found'], 404);
        }

        $confirmationCode = rand(100000, 999999);
        $organizer->confirmation_code = $confirmationCode;
        $organizer->save();

        Mail::to($organizer->email)->send(new OrganizerRegistrationConfirmation($confirmationCode));

        return response()->json(['message' => 'Confirmation code has been sent to your email']);
    }

    public function verifyConfirmationCode(Request $request)
    {
        $organizer = Organizer::where('email', $request->input('email'))->first();

        if (!$organizer) {
            return response()->json(['message' => 'Organizer not found'], 404);
        }

        if ($organizer->confirmation_code != $request->input('confirmation_code')) {
            return response()->json(['message' => 'Invalid confirmation code'], 400);
        }

        $organizer->email_verified_at = now();
        $organizer->save();

        return response()->json(['message' => 'Confirmation code is valid and organizer is now verified']);
    }
    public function login(LoginReq $request): JsonResponse
    {
        $token = GetAuthToken::execute($request->email, $request->password, provider: 'organizers');
        return $this->success($token);
    }
    public function sendPasswordResetCode(Request $request)
    {
        // Validate the request data
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        // Look up the organizer by email
        $organizer = Organizer::where('email', $request->email)->first();

        if (!$organizer) {
            return response()->json(['message' => 'Organizer not found'], 404);
        }

        // Generate and store the confirmation code
        $confirmationCode = rand(100000, 999999);
        $organizer->confirmation_code = $confirmationCode;
        $organizer->save();

        // Send the confirmation code to the organizer's email address
        Mail::to($organizer->email)->send(new OrganizerRegistrationConfirmation($confirmationCode));

        return response()->json(['message' => 'Password reset email sent'], 200);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => 'required|min:8'
        ]);

        $organizer = Organizer::where('email', $request->email)
            ->where('confirmation_code', $request->code)
            ->first();

        if (!$organizer) {
            return response()->json(['message' => 'Invalid email or confirmation code.'], 401);
        }

        $organizer->password = Hash::make($request->password);
        $organizer->confirmation_code = null;
        $organizer->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }
    public function indexOrganizerProfile()
    {
        $authenticatedUser = auth()->user();
        $organizer = Organizer::where('id', $authenticatedUser->id)->first();

        if (!$organizer) {
            return response()->json(['message' => 'Organizer not found'], 404);
        }

        return $organizer;
    }
    public function updateProfile(Request $request)
    {
        $authenticatedUser = auth()->user();
        
        // Get the user's current profile
        $user = Organizer::findOrFail($authenticatedUser->id);
        
        // Update the user's profile with the data from the request
        $user->fill($request->all());
        
        // Save the updated profile
        $user->save();
        
        // Return a success response
        return response()->json(['message' => 'Profile updated successfully']);
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
        $founderProfile = FounderProfile::find($founderUser->founder_id);

        $organizerId = Auth::id();
        if ($founderProfile->user_id != $organizerId) {
            return response()->json(['message' => 'You do not have permission to access this resource.'], 403);
        }

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
    
        // Check if the organizer has permission to update the founder user
        $organizerId = $request->user()->id;
        $founderProfile = FounderProfile::where('user_id', $organizerId)
                                         ->where('id', $founderUser->founder_id)
                                         ->first();
        if (!$founderProfile) {
            return response()->json(['message' => 'Unauthorized to update this founder user'], 403);
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
    public function getFounderUsersByFounderProfile(Request $request, $id)
    {
        // Get the FounderProfile by its ID
        $founderProfile = FounderProfile::find($id);
        if (!$founderProfile) {
            return response()->json(['message' => 'Founder profile not found'], 404);
        }
        // Check if the current organizer has permission to see this FounderProfile
        $organizerId = Auth::id();
        $founderProfileOrganizerId = $founderProfile->user_id;

        if ($organizerId !== $founderProfileOrganizerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the FounderUsers belonging to this FounderProfile
        $founderUsers = FounderUser::where('founder_id', $founderProfile->id)->get();

        // Get the User details for each FounderUser
        $founderUsersDetails = [];

        foreach ($founderUsers as $founderUser) {
            $user = User::find($founderUser->user_id);

            $founderUsersDetails[] = [
                'id' => $founderUser->id,
                'role' => $founderUser->role,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ];
        }
        // Return the FounderUsers details
        return response()->json(['founder_users' => $founderUsersDetails]);
    }
    public function getFounderProfiles()
    {
        $organizerId = Auth::user()->id;

        $founderProfiles = FounderProfile::where('user_id', $organizerId)
            ->with(['area', 'prefecture', 'offeredIncome'])
            ->get();
    
        return response()->json($founderProfiles);
    }
    public function getFounderProfile($id)
    {
        $organizerId = Auth::user()->id;

        $founderProfile = FounderProfile::where('id', $id)
            ->where('user_id', $organizerId)
            ->with(['area', 'prefecture', 'offeredIncome'])
            ->first();

        if (!$founderProfile) {
            return response()->json(['message' => 'Founder profile not found or unauthorized.'], 404);
        }

        return response()->json($founderProfile);
    }
    public function createFounderProfile(Request $request)
    {
        $organizerId = Auth::user()->id;
        $data = $request->all();

        DB::beginTransaction();

        if ($organizerId) {
            $fdrProfile = new FounderProfile();

            $update = Arr::only($data, [
                'company_name',
                'area_id',
                'prefecture_id',
                'is_listed_company',
                'no_of_employees',
                'capital',
                'last_year_sales',
                'established_on',
                'business_partner_company',
                'major_bank',
                'company_features',
                'job_description',
                'application_conditions',
                'employee_benefits',
                'offered_income_range_id',
                'work_start_date_4_entr'
            ]);

            $booleanFields = [
                'is_listed_company'
            ];

            foreach ($update as $column => $value) {
                if (Arr::exists($data, $column)) {
                    $fdrProfile->{$column} = in_array($column, $booleanFields) ? db_bool_val($value) : $value;
                }
            }

            $fdrProfile->user_id = $organizerId;
            $fdrProfile->save();

            UpdateCompanyIndustries::execute($fdrProfile, Arr::get($data, 'company_industry_ids', []));
            UpdateAfflCom::execute($fdrProfile, Arr::get($data, 'affiliated_companies', []));
            UpdateStockHolders::execute($fdrProfile, Arr::get($data, 'major_stock_holders', []));
            UpdateFdrPfdIndustries::execute($fdrProfile, Arr::get($data, 'pfd_industry_ids', []));
            UpdateFdrPfdPrefecture::execute($fdrProfile, Arr::get($data, 'pfd_prefecture_ids', []));
            UpdatePfdPositions::execute($fdrProfile, Arr::get($data, 'pfd_position_ids', []));

            DB::commit();

            return response()->json(['message' => 'Founder profile created successfully'], 201);
        }

        return response()->json(['error' => 'Organizer ID not found'], 400);
    }

}
