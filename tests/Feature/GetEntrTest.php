<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\AppBaseTestCase;

class GetEntrTest extends AppBaseTestCase
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

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->count(3)->create(['type' => User::ENTR]);
    }

    private function endpoint()
    {
        $user = User::entrepreneurs()->first();
        return '/api/v1/entr/' . $user->id;
    }

    public function test_forbidden_by_entr()
    {
        $this->authEntr();

        $req = $this->get($this->endpoint());
        $req->assertForbidden();
    }

    public function test_successful_access()
    {
        $this->authFounder();
        $req = $this->get($this->endpoint());
        $req->assertSuccessful();
    }
}
