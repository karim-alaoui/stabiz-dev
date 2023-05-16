<?php

namespace App\Http\Controllers\API;

use App\Actions\GetAuthToken;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\LoginReq;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

/**
 * @group Staff Auth
 *
 * Auth related to staff
 */
class AuthStaffController extends BaseApiController
{
    /**
     * Login for staff
     *
     * @unauthenticated
     * @responseFile storage/responses/auth_login_staff.json
     * @throws AuthenticationException
     */
    public function login(LoginReq $request): JsonResponse
    {
        $token = GetAuthToken::execute($request->email, $request->password, provider: 'staff');
        return $this->success($token);
    }
}
