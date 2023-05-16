<?php

namespace Tests\Unit;

use App\Actions\GetAuthToken;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use Tests\AppBaseTestCase;

class GetAuthTokenTest extends AppBaseTestCase
{
    /**
     * @throws AuthenticationException
     */
    public function test_token_generated()
    {
        $user = User::factory()->create();
        // test that token is generated
        $token = GetAuthToken::execute($user->email, 'password', $user->type);
        $this->assertTrue(Arr::has((array)$token, 'accessToken'));
    }

    /**
     * @throws Exception
     */
    public function test_token_generate_fail_()
    {
        try {
            $user = User::factory()->state(['type' => User::ENTR])->create();
            // if you provide different type than the user's type, then it would fail
            GetAuthToken::execute($user->email, 'password', User::FOUNDER);
            $this->fail(); // it can't reach this. If it reaches that means the test failed. It must throw the exception before reaching this line
        } catch (AuthenticationException) {
            $this->assertTrue(true);
        }
    }
}
