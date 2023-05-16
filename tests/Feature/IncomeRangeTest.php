<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

/**
 * Class IncomeRangeTest
 * @package Tests\Feature
 */
class IncomeRangeTest extends AppBaseTestCase
{
    use RefreshDatabase;

    public function test_if_range_exists()
    {
        $response = $this->get('/api/v1/income');
        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json->has('data')->etc());
    }
}
