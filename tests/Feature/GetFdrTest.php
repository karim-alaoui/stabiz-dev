<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\AppBaseTestCase;

/**
 * Class GetFdrTest
 * @package Tests\Feature
 */
class GetFdrTest extends AppBaseTestCase
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

    private string $base = '/api/v1/fdr';

    /**
     * @return string
     */
    private function endpoint(): string
    {
        $user = User::query()->founders()->first();
        return $this->base . '/' . $user->id;
    }

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->founder()->count(3)->create();
    }

    /**
     * Founders can not access other founders
     * Only entr can
     */
    public function test_forbidden_by_founder()
    {
        $this->authFounder();
        $req = $this->get($this->endpoint());
        $req->assertForbidden();
    }

    public function test_successful_access()
    {
        $this->authEntr();
        $req = $this->get($this->endpoint());
        $req->assertSuccessful();
    }
}
