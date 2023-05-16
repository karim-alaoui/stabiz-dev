<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

class UpdateStaffTest extends AppBaseTestCase
{
    use WithFaker;

    private string $endpoint = '/api/v1/staff';
    private Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $staff = Staff::factory()->create();
        $this->staff = $staff;
    }

    private function makeReq($data = [])
    {
        $this->authSuperAdmin();
        return $this->put(sprintf('%s/%s', $this->endpoint, $this->staff->id), $data);
    }

    public function test_should_send_success_on_no_input()
    {
        $res = $this->makeReq();
        $res->assertSuccessful();
    }

    public function test_unique_email()
    {
        $newstaff = Staff::factory()->create();
        $res = $this->makeReq(['email' => $newstaff->email]);
        $res->assertJsonValidationErrors(['email']);
    }

    public function test_forbidden()
    {
        $this->authStaff();
        $this->testForbidden(sprintf('%s/%s', $this->endpoint, $this->staff->id), 'put');
    }

    public function test_successful_update()
    {
        $data = [
            'first_name' => $this->faker->firstName('male'),
            'last_name' => $this->faker->lastName('female'),
            'email' => $this->faker->safeEmail(),
            'role' => Staff::SUPER_ADMIN_ROLE
        ];
        $res = $this->makeReq($data);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.first_name', Arr::get($data, 'first_name'))
                ->where('data.last_name', Arr::get($data, 'last_name'))
                ->where('data.email', Arr::get($data, 'email'))
                ->where('data.roles.0.name', Arr::get($data, 'role'))
                ->etc());
    }
}
