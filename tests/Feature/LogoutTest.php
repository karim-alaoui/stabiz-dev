<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

/**
 * Logout testing for both user and staff
 * Both use different endpoint
 * Class LogoutTest
 * @package Tests\Feature
 */
class LogoutTest extends AppBaseTestCase
{
    private string $endpointStaff = '/api/v1/logout/staff';
    private string $endpointUser = '/api/v1/logout/user';

    /**
     * @param $url
     */
    private function unauthorized($url)
    {
        $res = $this->post($url);
        $res->assertUnauthorized();
    }

    public function test_if_401_when_not_logged_in()
    {
        $this->unauthorized($this->endpointStaff);
    }

    public function test_if_401_not_logged_in_user()
    {
        $this->unauthorized($this->endpointUser);
    }

    public function test_logout_success_user()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        $res = $this->post($this->endpointUser);
        $res->assertNoContent(205);
    }

    public function test_logout_success_staff()
    {
        $this->authSuperAdmin();
        $res = $this->post($this->endpointStaff);
        $res->assertNoContent(205);
    }

    public function test_logout_everywhere()
    {
        $this->authSuperAdmin();
        $res = $this->post($this->endpointStaff . '/true');
        $res->assertNoContent(205);
    }
}
