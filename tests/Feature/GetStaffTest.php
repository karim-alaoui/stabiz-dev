<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

class GetStaffTest extends AppBaseTestCase
{
    private string $endpoint = '/api/v1/staff';

    public function test_get_staff()
    {
        Staff::factory()->count(10)->create();
        $data = [
            'first_name' => Str::random(),
            'last_name' => Str::random(),
            'email' => 'myemail@staff.com'
        ];
        $staff = Staff::factory()->create($data);

        $this->authSuperAdmin();
        $query = http_build_query(array_merge($data, ['id' => $staff->id]));
        $res = $this->get(sprintf('%s?%s', $this->endpoint, $query));

        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 1) // should return only one in the result. This proves that search is working
                ->has('data.0.first_name')
                ->has('data.0.roles') // this proves that it's loading the relationship
                ->etc());
    }

    public function test_pagination()
    {
        $this->authSuperAdmin();
        $this->testPagination($this->endpoint);
    }

    public function test_forbidden()
    {
        $this->authStaff(); // normal staff don't have access
        $this->testForbidden($this->endpoint);
    }
}
