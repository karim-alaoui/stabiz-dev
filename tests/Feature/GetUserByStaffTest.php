<?php

namespace Tests\Feature;

use App\Models\AssignedUserToStaff;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\SuperAdminStaffSeeder;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Tests\AppBaseTestCase;

/**
 * Test related to getting entrepreneurs
 * Class EntrTest
 * @package Tests\Feature
 */
class GetUserByStaffTest extends AppBaseTestCase
{
    private string $endpoint = '/api/v1/users';

    /**
     * @param array $query
     * @return TestResponse
     */
    private function getReq(array $query = []): TestResponse
    {
        $url = sprintf('%s?%s', $this->endpoint, http_build_query($query));
        return $this->get(trim($url));
    }

    public function test_get_entrs()
    {
        $this->authSuperAdmin();
        $res = $this->getReq(['type' => User::ENTR]);
        $res->assertStatus(200);
        // assert that relationships are loaded too
        $res->assertJson(fn(AssertableJson $json) => $json
            ->etc());
    }

    public function test_forbidden()
    {
        $this->authStaff(); // only super admin allowed not any staff
        $res = $this->getReq(['type' => User::ENTR]);
        $res->assertForbidden();
    }

    public function test_pagination()
    {
        $this->authSuperAdmin();
        $this->testPagination(sprintf('%s?type=founder', $this->endpoint));
    }

    private function nameSearch($type)
    {
        $this->authSuperAdmin();
        $firstName = Str::random();
        $lastName = Str::random();
        User::factory()->count(3)->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'type' => $type
        ]);

        $res = $this->getReq(['first_name' => $firstName, 'last_name' => $lastName, 'type' => $type]);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 3)
                ->where('data.0.type', $type)
                ->etc());
    }

    public function test_name_search()
    {
        $this->nameSearch(User::FOUNDER);
        $this->nameSearch(User::ENTR);
    }

    /**
     * Add the staff that was assigned to an user
     */
    public function test_assigned_staff_with_users()
    {
        User::factory()->count(10)->create();
        $this->seed(SuperAdminStaffSeeder::class);
        $superAdmin = Staff::role(Staff::SUPER_ADMIN_ROLE)->first();

        // add fake data
        User::query()
            ->select(['id'])
            ->get()
            ->pluck('id')
            ->map(function ($id) use ($superAdmin) {
                // don't assign staff for all of them
                // only to a few for testing
                if ($id % 3 == 0) {
                    $staff = Staff::factory()->create();
                    AssignedUserToStaff::create([
                        'user_id' => $id,
                        'staff_id' => $staff->id,
                        // only a super admin can assign
                        'added_by_staff_id' => $superAdmin->id
                    ]);
                }
            });

        $this->authSuperAdmin();
        $endpoint = sprintf('%s?%s', $this->endpoint, http_build_query(['add_assigned_staff' => true, 'type' => 'founder,entrepreneur']));

        $req = $this->get($endpoint);
        $json = $req->json('data');

        $req->assertSuccessful()
            // check if assigned_staff is added since we added add_assigned_staff = true in the request
            ->assertJson(fn(AssertableJson $json) => $json->has('data.0.assigned_staff')
                ->etc()
            );

        array_map(function ($user) {
            if ($user['id'] % 3 == 0) {
                // we only assigned staff for user above whose id is divided by 3
                $this->assertNotEmpty($user['assigned_staff'][0]['staff']['id']); // check if the staff was returned for this user
            } else {
                // check if there was no staff
                // since we didn't assign any staff. it would return an empty array for assigned_staff
                $this->assertArrayNotHasKey('0.staff.id', $user['assigned_staff']);
            }
        }, $json);
    }
}
