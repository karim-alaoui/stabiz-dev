<?php /** @noinspection ALL */

namespace Tests\Feature;

use App\Models\User;
use Tests\AppBaseTestCase;

/**
 * An account can be deleted by the user himself or superadmin
 */
class DeleteAccountTest extends AppBaseTestCase
{
    private string $endpoint = 'api/v1/user';
    private $user;

    private function makeReq($data = [], $includePass = true)
    {
        $user = $this->authUser();
        $this->user = $user;
        if ($includePass) {
            $data = array_merge($data, [
                'password' => 'password',
                'confirm_password' => 'password'
            ]);
        }
        return $this->delete($this->endpoint, $data);
    }

    public function test_unauth()
    {
        $res = $this->delete($this->endpoint);
        $res->assertStatus(401);
    }

    public function test_validation_error()
    {
        $res = $this->makeReq(includePass: false);
        $res->assertJsonValidationErrors(['password', 'confirm_password']);
    }

    public function test_password_mismatch_validation()
    {
        $res = $this->makeReq(['password' => 'password', 'confirm_password' => 'pass not match'], includePass: false);
        $res->assertJsonValidationErrors(['confirm_password'])
            ->assertJsonMissingValidationErrors(['password']);
    }

    public function test_password_mismatch()
    {
        $res = $this->makeReq(['password' => 'passwordmismatch', 'confirm_password' => 'passwordmismatch'], false);
        // default password is password. Check if mismatch or not
        $res->assertStatus(401);
    }

    public function test_deleted_account()
    {
        $res = $this->makeReq();
        $res->assertStatus(204);

        // check if the user account is soft deleted and
        // all his personal info is erased or not
        $user = User::withTrashed()->find($this->user->id);
        $this->assertTrue(is_null($user->email));
        $this->assertTrue(is_null($user->first_name));
        $this->assertTrue(is_null($user->last_name));
        $this->assertTrue(is_null($user->gender));
        $this->assertTrue(is_null($user->dob));
        $this->assertTrue(!is_null($user->deleted_at)); // must have since it's soft deleted
    }

    public function test_deleted_by_admin()
    {
        $this->authSuperAdmin();
        $user = $this->createFdr();
        $res = $this->delete('api/v1/users/' . $user->id);
        $res->assertStatus(204);
    }

    public function test_if_forbidden_by_staff()
    {
        $this->authStaff();
        $user = $this->createFdr();
        $res = $this->delete('api/v1/users/' . $user->id);
        $res->assertForbidden();
    }

    public function test_if_unauth_by_user()
    {
        $this->authUser();
        $user = $this->createFdr();
        $res = $this->delete('api/v1/users/' . $user->id); // when a normal user tries to access this, it should return 401
        $res->assertUnauthorized();
    }
}
