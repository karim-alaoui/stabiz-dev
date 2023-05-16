<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

/**
 * Update user password test
 * Class UpdatePassUserTest
 * @package Tests\Feature
 */
class UpdatePassUserTest extends AppBaseTestCase
{
    private string $endpoint = '/api/v1/user/update-password';

    public function test_validation()
    {
        $this->authUser();
        $res = $this->patch($this->endpoint);
        $res->assertJsonValidationErrors(['current_password', 'password', 'confirm_password']);
    }

    public function test_wrong_current_password()
    {
        $this->authUser();
        $res = $this->patch($this->endpoint, [
            'current_password' => 'random password',
            'password' => 'new password',
            'confirm_password' => 'new password'
        ]);

        $res->assertStatus(400)
            ->assertJsonPath('message', 'Current password do not match');
    }

    public function test_same_password_as_old_password()
    {
        $this->authUser();
        $res = $this->patch($this->endpoint, [
            'current_password' => 'password',
            'password' => 'password',
            'confirm_password' => 'password'
        ]);

        $res->assertStatus(400)
            ->assertJsonPath('message', 'New password and old password can not be same');
    }

    public function test_password_change_success()
    {
        $user = User::factory()->create();
        $email = $user->email;
        Passport::actingAs($user);

        $res = $this->patch($this->endpoint, [
            'current_password' => 'password',
            'password' => 'new password',
            'confirm_password' => 'new password'
        ]);

        $res->assertSuccessful();

        // login with the new password now
        $type = $user->type;
        // the endpoint will differ based on different user type
        if ($type == User::ENTR) {
            $endpoint = 'api/v1/login/entr';
        } else {
            $endpoint = 'api/v1/login/fdr';
        }
        $login = $this->post($endpoint, ['email' => $email, 'password' => 'new password']);
        $login->assertSuccessful();

        // try with the old password and check if unsuccessful
        $old = $this->post($endpoint, ['email' => $email, 'password' => 'password']);
        $old->assertUnauthorized();
    }
}
