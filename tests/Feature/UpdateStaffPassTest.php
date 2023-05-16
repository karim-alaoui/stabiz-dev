<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

class UpdateStaffPassTest extends AppBaseTestCase
{

    private string $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $staff = Staff::factory()->create();
        $this->endpoint = '/api/v1/staff/password/' . $staff->id;
    }

    /**
     * @param array $data
     * @return TestResponse
     */
    private function makeReq($data = []): TestResponse
    {
        $this->authSuperAdmin();
        return $this->patch($this->endpoint, $data);
    }

    public function test_validation()
    {
        $res = $this->makeReq();
        $res->assertJsonValidationErrors(['password', 'confirm_password']);
    }

    public function test_password_validation()
    {
        $res = $this->makeReq(['password' => Str::random(), 'confirm_password' => 'not match']);
        $res->assertJsonValidationErrors(['confirm_password'])
            ->assertJsonMissingValidationErrors(['password']);
    }

    public function test_if_changed()
    {
        $this->authSuperAdmin();
        $staff = Staff::factory()->create();
        $password = Str::random();
        $res = $this->patch('/api/v1/staff/password/' . $staff->id, ['password' => $password, 'confirm_password' => $password]);
        $res->assertSuccessful();

        // now test if able to login using the new password
        $loginurl = 'api/v1/login/staff';
        $failRes = $this->post($loginurl, ['email' => $staff->email, 'password' => 'random password']);
        $failRes->assertStatus(401);

        $successres = $this->post($loginurl, ['email' => $staff->email, 'password' => $password]);
        $successres->assertSuccessful();
    }

    public function test_own_password()
    {
        // a normal staff can only change his/her own password and not anybody else's
        $staff = Staff::factory()->create();
        $staff2 = Staff::factory()->create();
        Passport::actingAs($staff, guard: $this->staffGuard);

        $pass = Str::random();
        $fail = $this->patch(sprintf('%s/%s', 'api/v1/staff/password', $staff2->id), [
            'password' => $pass,
            'confirm_password' => $pass
        ]);
        $fail->assertForbidden();

        $res = $this->patch(sprintf('%s/%s', 'api/v1/staff/password', $staff->id), [
            'password' => $pass,
            'confirm_password' => $pass
        ]);

        $res->assertSuccessful();
    }
}
