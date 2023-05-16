<?php

namespace Tests\Feature;

use App\Models\Staff;
use Tests\AppBaseTestCase;

class DestoryStaffTest extends AppBaseTestCase
{
    private string $url;
    private Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $staff = Staff::factory()->create();
        $this->staff = $staff;
        $this->url = sprintf('%s/%s', 'api/v1/staff', $staff->id);
    }

    public function test_successful()
    {
        $this->authSuperAdmin();
        $res = $this->delete($this->url);
        $res->assertStatus(204);

        // check if item is soft deleted
        $this->assertTrue((bool)Staff::withTrashed()->where('id', $this->staff->id)->first());
    }

    public function test_forbidden()
    {
        $this->authStaff();
        $res = $this->delete($this->url);
        $res->assertForbidden();
    }
}
