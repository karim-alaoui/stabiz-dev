<?php

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

/**
 * Test for some basic values that are used throughout the app
 * Values like prefectures etc.
 * Class ValueControllerTest
 * @package Tests\Feature
 */
class ValueControllerTest extends AppBaseTestCase
{
    protected array $endpoints = [
        'category-for-article',
        'income',
        'prefecture',
        'area',
        'education-backgrounds',
        'working-status',
        'lang-level',
        'occupation',
        'present-post',
        'lang',
        'industry',
        'industry-categories',
        'position',
        'package',
        'plan',
        'occupation-categories',
        'mgmt-exp'
    ];

    /**
     * This will check if the endpoints actually exist
     * and return data
     * @return void
     */
    public function test_if_data_exists()
    {
        $base = 'api/v1';
        foreach ($this->endpoints as $endpoint) {
            $response = $this->get("$base/$endpoint");
            $response->assertStatus(200);
            $response->assertJson(fn(AssertableJson $json) => $json->has('data')->etc());

            $data = $response->json();
            // if data exists, the data key would have some values, otherwise, there would be nothing.
            // when it works, some data would definitely be there if the database was seeded properly
            $this->assertTrue((bool)count($data['data']));
        }
    }
}
