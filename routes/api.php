<?php

use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\AssignToStaffController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrganizerController;
use App\Http\Controllers\API\AuthStaffController;
use App\Http\Controllers\API\MailTemplateController;
use App\Http\Controllers\API\NewsTopicController;
use App\Http\Controllers\API\RecommendController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\UploadDocController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserMgmt;
use App\Http\Controllers\API\UsrPaymentMthd;
use App\Http\Controllers\API\ValueController;
use App\Http\Controllers\API\VideoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::prefix('v1')->group(function () {
    Route::post('otp-send', [AuthController::class, 'sendOTP']);
    Route::post('otp-verify', [AuthController::class, 'verifyOTP']);
    Route::post('login/entr', [AuthController::class, 'loginEntr'])->name('login.user_entr');
    Route::post('login/fdr', [AuthController::class, 'loginFdr'])->name('login.user_fdr');
    
//    Route::post('login', [AuthController::class, 'login'])->name('login.user');
    Route::post('login/staff', [AuthStaffController::class, 'login'])->name('login.staff');

    Route::post('register/organizer', [OrganizerController::class, 'registerOrganizer']);
    Route::post('resend-confirmation-code', [OrganizerController::class, 'resendConfirmationCode']);
    Route::post('verify-confirmation-code', [OrganizerController::class, 'verifyConfirmationCode']);
    Route::post('organizer/login', [OrganizerController::class, 'login']);
    Route::post('organizer/forget-password', [OrganizerController::class, 'sendPasswordResetCode']);
    Route::post('organizer/reset-password', [OrganizerController::class, 'resetPassword']);

    Route::post('register/{type}', [AuthController::class, 'register']);
    Route::post('forget-password', [AuthController::class, 'forgetPassword'])
        ->name('password_reset.otp');
    Route::post('reset-password', [AuthController::class, 'resetPass'])
        ->name('password_reset')
        ->middleware('throttle:20,1');

    Route::get('category-for-article', [ValueController::class, 'category4articles']);
    Route::get('income', [ValueController::class, 'income']);
    Route::get('mgmt-exp', [ValueController::class, 'managementExps']);
    Route::get('prefecture', [ValueController::class, 'prefecture']);
    Route::get('area', [ValueController::class, 'area']);
    Route::get('education-backgrounds', [ValueController::class, 'eduBg']);
    Route::get('working-status', [ValueController::class, 'workingStatus']);
    Route::get('industry', [ValueController::class, 'industry']);
    Route::get('industry-categories', [ValueController::class, 'industryCat']);
    Route::get('lang-level', [ValueController::class, 'langLevel']);
    Route::get('occupation', [ValueController::class, 'occupation']);
    Route::get('present-post', [ValueController::class, 'presentPost']);
    Route::get('lang', [ValueController::class, 'language']);
    Route::get('news-topic', [NewsTopicController::class, 'indexUserSide']);
    Route::get('position', [ValueController::class, 'position']);
    Route::get('occupation-categories', [ValueController::class, 'occupationCat']);
    Route::get('package', [ValueController::class, 'packages']);
    Route::get('plan', [ValueController::class, 'plans']);

    Route::middleware('auth:api-organizer')->group(function () {
        // Routes for authenticated organizers
        Route::get('/organizers', [OrganizerController::class, 'index']);
        Route::get('/organizers/{id}', [OrganizerController::class, 'show']);
        Route::get('organizer/profile', [OrganizerController::class, 'indexOrganizerProfile']);
        Route::put('organizer/profile', [OrganizerController::class, 'updateProfile']);
        Route::post('organizer/founder-users', [OrganizerController::class, 'createFounderUser']);
        Route::get('organizer/founder-users/{userId}', [OrganizerController::class, 'getFounderUserDetails']);
        Route::put('organizer/founder-users/{userId}', [OrganizerController::class, 'updateFounderUser']);
        Route::get('organizer/founder-profiles/{id}/founder-users', [OrganizerController::class, 'getFounderUsersByFounderProfile']);
        Route::get('organizer/founder-profiles',  [OrganizerController::class, 'getFounderProfiles']);
        Route::get('organizer/founder-profiles/{id}', [OrganizerController::class, 'getFounderProfile']);
        Route::post('organizer/founder-profiles', [OrganizerController::class, 'createFounderProfile']);

        // Add more routes here as needed
        Route::get('/test', function () {
            return response()->json(['message' => 'This is a test endpoint for organizers.']);
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('user', [UserController::class, 'getUser']);
        Route::delete('user', [UserController::class, 'delete']);
        Route::put('user/entrepreneur', [UserController::class, 'updateEntr']);
        Route::put('user/fdr', [UserController::class, 'updateFdr']);
        Route::patch('user/update-password', [UserController::class, 'updatePass']);
        Route::post('logout/user/{logout_everywhere?}', [AuthController::class, 'logout'])->name('logout');
        Route::post('/update-dp', [UserController::class, 'updateDP']);
        Route::post('/update-company-imgs', [UserController::class, 'updateCompanyImgs']);

        Route::get('entrepreneurs', [UserController::class, 'getEntrepreneursList']);
        Route::get('founders', [UserController::class, 'getFoundersList']);
        Route::get('entrepreneurs/{id}', [UserController::class, 'getEntrepreneurDetails']);
        Route::get('founders/{id}', [UserController::class, 'getFounderProfileDetails']);
        Route::put('/applications/{id}/agreeNDA', [ApplicationController::class, 'agreeToNDA']);

        // search founders can be done by entrepreneurs
        Route::get('/search-fdr', [SearchController::class, 'searchFdr']);
        Route::get('/fdr/{founder}', [SearchController::class, 'getFdr']);
        Route::get('/entr/{entrepreneur}', [SearchController::class, 'getEntr']);
        Route::get('/search-entr', [SearchController::class, 'searchEntr']);
        Route::post('/applications', [ApplicationController::class, 'apply']);
        Route::post('/applications/reject/{application}', [ApplicationController::class, 'reject']);
        Route::post('/applications/accept/{application}', [ApplicationController::class, 'accept']);
        Route::get('/applications/applied', [ApplicationController::class, 'applied']);
        Route::get('/applications/check/{appliedToUserId}', [ApplicationController::class, 'checkIfApplied']);
        Route::get('/applications/recvd', [ApplicationController::class, 'recvdApl']);
        Route::post('/applications/{application}/applicant-detail', [ApplicationController::class, 'applicantDetails']);
        Route::post('setup-intent', [SubscriptionController::class, 'returnIntent']);
        Route::apiResource('user/payment-method', UsrPaymentMthd::class)->only(['index', 'destroy']);
        Route::get('recommended-users', [RecommendController::class, 'recList4User']);

        Route::prefix('user')->group(function () {
            Route::delete('payment-method', [UsrPaymentMthd::class, 'destroyAll']);
            Route::apiResource('subscription', SubscriptionController::class)
                ->only(['index', 'store']);
            Route::delete('subscription', [SubscriptionController::class, 'destroyAll']);
            Route::apiResource('docs', UploadDocController::class)->except(['update']);
        });
    });

    Route::middleware('auth:api-staff')->group(function () {
        Route::post('logout/staff/{logout_everywhere?}', [AuthController::class, 'logout'])->name('logout');
        Route::get('staff/authenticated', [StaffController::class, 'staff']);
        Route::patch('staff/password/{staff}', [StaffController::class, 'updatePass']);
        Route::apiResource('staff', StaffController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('news-n-topics', NewsTopicController::class);
        Route::apiResource('users', UserMgmt::class)->only(['destroy', 'show', 'index']);
        Route::put('user/entrepreneur/{user}', [UserMgmt::class, 'updateEntr']);
        Route::put('user/fdr/{user}', [UserMgmt::class, 'updateFdr']);
        Route::apiResource('assign-users', AssignToStaffController::class)->except(['update']);
        Route::apiResource('videos', VideoController::class);
        Route::apiResource('email-template', MailTemplateController::class);
        Route::delete('clear-cache', [ValueController::class, 'clearCache']);
        Route::apiResource('article', ArticleController::class);
//        Route::apiResource('master-coupon', MasterCouponController::class);
        Route::post('recommend', [RecommendController::class, 'recommend']);
        Route::get('rec-users-search', [RecommendController::class, 'recUserSearch']);
        Route::get('docs/{id}', [UploadDocController::class, 'show']);
        Route::get('docs', [UploadDocController::class, 'lstDocs']);
        Route::post('docs/verify', [UploadDocController::class, 'verify']);
        Route::get('recommended-users/{user}', [RecommendController::class, 'accessRcmdListOfUser']);
        Route::delete('recommended-users/{user}/{removeUser}', [RecommendController::class, 'removeFromRcmdLst']);

        Route::get('staff/founder-profiles/{id}/founder-users', [StaffController::class, 'getFounderUsersByFounderProfile']);
        Route::post('staff/founder-users', [StaffController::class, 'createFounderUser']);
        Route::get('staff/founder-users/{userId}', [StaffController::class, 'getFounderUserDetails']);
        Route::put('staff/founder-users/{userId}', [StaffController::class, 'updateFounderUser']);
        Route::get('staff/organizers', [StaffController::class, 'getOrganizers']);
        Route::get('staff/organizers/{userId}', [StaffController::class, 'indexOrganizerProfile']);
        Route::get('staff/organizers/{userId}/founder-profiles',  [StaffController::class, 'getOrganizerFounderProfiles']);

        Route::get('staff/applications', [ApplicationController::class, 'getAllApplications']);
        Route::put('staff/applications/{id}', [ApplicationController::class, 'update']);

    });

    Route::middleware(['auth:api'])->group(function () {
        Route::get('user-detail/{id}', [UserMgmt::class, 'show']);
    });
});
