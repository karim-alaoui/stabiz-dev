<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

class LoginStaffTest extends AppBaseTestCase
{
    protected string $endpoint = 'api/v1/login/staff';

    protected string $email = 'staff@email.com';
    protected string $password = 'staff_password837123&8**';

    protected function seedStaff()
    {
        Staff::factory()
            ->count(10)
            ->create();

        Staff::factory()
            ->create([
                'email' => $this->email,
                'password' => Hash::make($this->password)
            ]);
    }

    public function test_if_method_post()
    {
        $res = $this->get($this->endpoint);
        $res->assertStatus(405);
    }

    public function test_if_validation_error()
    {
        $res = $this->post($this->endpoint);
        $res->assertJsonValidationErrors([
            'email',
            'password'
        ]);
    }

    public function test_if_fail_on_wrong_password()
    {
        $this->seedStaff();

        $res = $this->post($this->endpoint, [
            'email' => $this->email,
            'password' => 'staff'
        ]);

        $res->assertStatus(401);

        $res = $this->post($this->endpoint, [
            'email' => $this->email,
            'password' => substr($this->password, 1)
        ]);

        $res->assertStatus(401);
    }

    public function test_if_login_success()
    {
        $this->seedStaff();
        $res = $this->post($this->endpoint, [
            'email' => $this->email,
            'password' => $this->password
        ]);

        $res->assertSuccessful();
        $res->assertJson(fn(AssertableJson $json) => $json
            ->has('accessToken')
            ->etc());
    }

}
