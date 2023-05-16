<?php

namespace Tests\Feature;

use App\Models\AssignedUserToStaff;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

/**
 * Class AssignUserToStaffTest
 * @package Tests\Feature
 */
class AssignUserToStaffTest extends AppBaseTestCase
{
    private string $endpoint = '/api/v1/assign-users';

    /**
     * @param int $count
     */
    private function insertData(int $count = 4)
    {
        AssignedUserToStaff::factory()->count($count)->create();
    }

    public function test_add_assign_validation_errors()
    {
        $this->authSuperAdmin();
        $res = $this->post($this->endpoint);
        $res->assertJsonValidationErrors(['staff_id', 'user_id']);

        // supply random user ids that doesn't exist in the database and see if it returns validation error
        $staff = Staff::factory()->create();
        $res = $this->post($this->endpoint, ['staff_id' => $staff->id, 'user_id' => [219030128, 128931289, 172301289]]);
        $res->assertJsonValidationErrors(['user_id'])
            ->assertJsonMissingValidationErrors(['staff_id']);
    }

    public function test_add_assign_success()
    {
        $superadmin = $this->authSuperAdmin();
        $staff = Staff::factory()->create();
        $staff->assignRole(Staff::MATCH_MAKER_ROLE);

        User::factory()->count(5)->create();
        $userId = User::all()->pluck('id')->toArray();
        $res = $this->post($this->endpoint, [
            'user_id' => $userId,
            'staff_id' => $staff->id
        ]);

        $res->assertCreated();
        $table = (new AssignedUserToStaff())->getTable();
        $this->assertDatabaseCount($table, count($userId));
        $this->assertDatabaseHas($table, ['added_by_staff_id' => $superadmin->id]);
    }

    public function test_assign_show()
    {
        $this->authSuperAdmin();
        $this->insertData();
        $first = AssignedUserToStaff::first()->id;
        $res = $this->get($this->endpoint . '/' . $first);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.staff.first_name')
                ->has('data.user.first_name')
                ->etc());
    }

    public function test_assign_show_by_staff()
    {
        // a normal staff should only have access to the one which was assigned to him
        // not to any other one
        $this->insertData();
        $assigned = AssignedUserToStaff::first();
        $staffId = $assigned->staff_id;
        /**@var Staff $staff */
        $staff = Staff::findOrFail($staffId);
        $staff->roles()->detach();
        $staff->assignRole(Staff::MATCH_MAKER_ROLE);

        Passport::actingAs($staff, guard: $this->staffGuard);
        $url = $this->endpoint . '/' . $assigned->id;
        $res = $this->get($url);
        $res->assertSuccessful();

        $other = AssignedUserToStaff::factory()->create();
        $res = $this->get($this->endpoint . '/' . $other->id);
        $res->assertForbidden();
    }

    public function test_get_assigned()
    {
        $this->authSuperAdmin();
        $this->insertData(10);
        $res = $this->get($this->endpoint);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', 10) // should return all the 10 results since 15 items is the limit
                ->has('data.0.staff.first_name')
                ->has('data.0.user.first_name')
                ->etc());
    }

    public function test_get_by_staff()
    {
        // a staff should only get the ones returned to him
        $this->insertData(10);
        $assigned = AssignedUserToStaff::first();
        /**@var Staff $staff */
        $staff = Staff::find($assigned->staff_id);
        $staff->roles()->detach();
        $staff->assignRole(Staff::MATCH_MAKER_ROLE);
        $count = AssignedUserToStaff::where('staff_id', $staff->id)->count();

        Passport::actingAs($staff, guard: $this->staffGuard);
        $res = $this->get($this->endpoint);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', $count)
                ->etc());
    }

    private function deleteAssign()
    {
        $first = AssignedUserToStaff::first();
        return $this->delete($this->endpoint . '/' . $first->id);
    }

    public function test_delete_assign_by_staff()
    {
        $this->authStaff();
        $this->insertData();
        $res = $this->deleteAssign();
        $res->assertForbidden();
    }

    public function test_delete_assign()
    {
        $this->authSuperAdmin();
        $this->insertData();
        $count = AssignedUserToStaff::count();
        $res = $this->deleteAssign();
        $res->assertNoContent();
        $this->assertDatabaseCount((new AssignedUserToStaff())->getTable(), $count - 1);
    }
}
