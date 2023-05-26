<?php

namespace App\Http\Controllers\API;

use App\Actions\CreateUser;
use App\Actions\GetAuthToken;
use App\Actions\LogoutEverywhere;
use App\Actions\ResetPass;
use App\Actions\SendOTP;
use App\Actions\VerifyOTP;
use App\Exceptions\ActionException;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\ForgetPassReq;
use App\Http\Requests\LoginReq;
use App\Http\Requests\RegReq;
use App\Http\Requests\ResetPassReq;
use App\Http\Requests\SendOTPReq;
use App\Http\Requests\VerifyOTPReq;
use App\Http\Resources\UserResource;
use App\Models\Staff;
use App\Models\User;
use App\Rules\UniqueEmailForUserType;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group User Auth
 * APIs for sending OTP, sign up, login and anything auth related etc
 * Class AuthController
 * @package App\Http\Controllers\API
 */
class AuthController extends BaseApiController
{
    /**
     * Send OTP
     *
     * <aside class="notice">
     * Note - An OTP is valid only for 5 mins. If you apply for multiple otps,
     * only the one requested at the end is valid, the moment you opt for one otp,
     * all the previous one which you requested but never used become invalid. This is done for security reason.
     * </aside>
     * @unauthenticated
     * @param SendOTPReq $request
     * @return JsonResponse
     * @throws ActionException
     */
    public function sendOTP(SendOTPReq $request): JsonResponse
    {
        SendOTP::execute(email: $request->email, registration: $request->boolean('registration'));
        return $this->successMsg(__('message.otp_sent'));
    }

    /**
     * Verify Email
     *
     * Verify the email address by providing otp
     * @unauthenticated
     * @param VerifyOTPReq $request
     * @return JsonResponse
     * @throws ActionException
     */
    public function verifyOTP(VerifyOTPReq $request): JsonResponse
    {
        VerifyOTP::execute($request->email, $request->otp, $request->user_type, true);
        return $this->successMsg(__('message.otp_verified'));
    }
    
    /**
     * Login for entrepreneur
     *
     * @param LoginReq $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function loginEntr(LoginReq $request): JsonResponse
    {
        $token = GetAuthToken::execute($request->email, $request->password, User::ENTR);
        return $this->success($token);
    }
    /**
     * Login for founder
     *
     * @param LoginReq $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function loginFdr(LoginReq $request): JsonResponse
    {
        $token = GetAuthToken::execute($request->email, $request->password, User::FOUNDER);
        return $this->success($token);
    }

    /**
     * Register
     *
     * Currently, both the entrepreneur and founder reg endpoints have the same
     * request body. It will change in the future.
     * @urlParam type required either `entrepreneur` or `founder` No-example
     * @unauthenticated
     * @responseFile status=201 storage/responses/register.json
     * @throws ActionException
     * @throws Exception
     */
    public function register(RegReq $request, $type): JsonResponse
    {
        $type = strtolower($type);
        if (!in_array($type, [User::ENTR])) {
            throw new Exception(__('Invalid type provided'));
        }
        $request->validate([
            'email' => new UniqueEmailForUserType($type)
        ]);
        $user = CreateUser::execute($request->validated(), $type, true);
        return (new UserResource($user))->response()->setStatusCode(201);
    }

    /**
     * Forget password OTP
     *
     * Forget password will send an OTP on the user's email.
     * Show the msg sent from backend to the user or something similar.
     * @unauthenticated
     * @param ForgetPassReq $request
     * @return JsonResponse
     * @throws ActionException
     */
    public function forgetPassword(ForgetPassReq $request): JsonResponse
    {
        // only send the otp if an user with that email exists
        if (User::where('email', $request->email)->first()) SendOTP::execute($request->email, __('phrase.otp_subject'));
        $msg = 'If your email exists in our system, you will receive an OTP on your email soon';
        return $this->successMsg(__($msg));
    }

    /**
     * Reset password
     *
     * After receiving otp, enter the otp, new password here
     * @unauthenticated
     * @param ResetPassReq $request
     * @return mixed
     * @throws ActionException
     * @throws AuthenticationException
     */
    public function resetPass(ResetPassReq $request): mixed
    {
        // it will return new access token after resetting the password
        return ResetPass::execute(
            $request->email,
            $request->otp,
            $request->password,
            $request->user_type
        );
    }

    /**
     * Logout
     *
     * This logout is for both staff and user
     * @urlParam logout_everywhere send as `true` or `1` or `on` if you want to logout the user from everywhere Example: true
     * @param Request $request
     * @param $logout_everywhere
     * @return Response
     */
    public function logout(Request $request, $logout_everywhere = null): Response
    {
        /**@var Staff|User $user */
        $user = auth()->user();
        $logout_everywhere = $request->boolean($logout_everywhere);
        if ($logout_everywhere) LogoutEverywhere::execute($user);
        else $user->token()->revoke();

        return $this->noContent(205);
    }
}
