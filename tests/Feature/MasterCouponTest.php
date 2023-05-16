<?php

namespace Tests\Feature;

use Tests\AppBaseTestCase;

/**
 * Class MasterCouponTest
 * @package Tests\Feature
 */
class MasterCouponTest extends AppBaseTestCase
{
    private string $endpoint = '/api/v1/master-coupon';

    /*public function test_validation_error()
    {
        $this->authSuperAdmin();
        $res = $this->post($this->endpoint);
        $res->assertJsonValidationErrors(['name', 'amount_off', 'percent_off', 'duration', 'assign_after', 'is_a_campaign']);
    }

    public function test_optional_validation()
    {
        $this->authSuperAdmin();
        $data = [
//          'percent_off' =>
        ];
        $res = $this->post($this->endpoint);
//        $res->
    }*/
}
