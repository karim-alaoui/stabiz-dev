<?php

namespace Tests\Feature;

use Tests\AppBaseTestCase;

class ClearCacheTest extends AppBaseTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_clear_cache()
    {
        $this->authSuperAdmin();
        $endpoint = '/api/v1/clear-cache';
        $req = $this->delete($endpoint);
        $req->assertNoContent();
    }
}
