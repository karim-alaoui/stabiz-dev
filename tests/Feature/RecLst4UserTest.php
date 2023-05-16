<?php

namespace Tests\Feature;

use App\Models\Recommendation;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

class RecLst4UserTest extends AppBaseTestCase
{
    private string $endpoint = 'api/v1/recommended-users';

    public function test_list()
    {
        $entr = $this->authEntr();
        Recommendation::factory()
            ->count(10)
            ->state([
                'recommended_to_user_id' => $entr->id
            ])
            ->create();

        $request = $this->get($this->endpoint);
        $request->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.0.recommended_user.first_name')
                ->etc());

        $this->testPagination($this->endpoint);
    }

    /**
     * It's not accessible by the staff. Only by the user
     */
    public function test_only_accessed_by_user()
    {
        $this->authStaff();
        $req = $this->get($this->endpoint);
        $req->assertUnauthorized();
    }
}
