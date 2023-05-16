<?php

namespace Tests\Feature;

use App\Models\Recommendation;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

/**
 * Access the recommended list of users of an user
 */
class RecLstAccessByStaff extends AppBaseTestCase
{
    private function endpoint(int $userid): string
    {
        return 'api/v1/recommended-users/' . $userid;
    }

    public function test_list()
    {
        // check if list is accessible or not
        $this->authStaff();
        Recommendation::factory()
            ->count(10)
            ->create();

        $userId = Recommendation::query()->first()->recommended_to_user_id;
        $endpoint = $this->endpoint($userId);
        $req = $this->get($endpoint);
        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.0.recommended_user.first_name')
                ->etc());
    }

    public function test_no_access_to_user()
    {
        // user side should not be able to access this endpoint
        $this->authUser();
        Recommendation::factory()
            ->count(10)
            ->create();

        $userId = Recommendation::query()->first()->recommended_to_user_id;
        $endpoint = $this->endpoint($userId);
        $req = $this->get($endpoint);
        $req->assertUnauthorized();
    }
}
