<?php

namespace Tests\Feature;

use App\Models\Recommendation;
use App\Models\User;
use Tests\AppBaseTestCase;

class RecommendTest extends AppBaseTestCase
{
    private string $rcmdEndpoint = '/api/v1/recommend';

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()
            ->count(2)
            ->state([
                'type' => User::ENTR
            ])
            ->create();

        User::factory()
            ->count(2)
            ->state([
                'type' => User::FOUNDER
            ])
            ->create();
    }

    public function test_validation()
    {
        $this->authStaff();
        $response = $this->post($this->rcmdEndpoint);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['recommended_to_user_id', 'recommended_user_id']);
    }

    public function test_unauth()
    {
        // can only be accessed by staff
        // for user, it will be unauthenticated
        $this->authUser();
        $response = $this->post($this->rcmdEndpoint);
        $response->assertUnauthorized();
    }

    public function test_recommend()
    {
        $this->authStaff();
        $data = [
            'recommended_to_user_id' => User::entrepreneurs()->first()->id,
            'recommended_user_id' => User::founders()->first()->id
        ];
        $response = $this->post($this->rcmdEndpoint, $data);
        $response->assertSuccessful();

        // should not able to recommend again as they are already recommended
        $response2 = $this->post($this->rcmdEndpoint, $data);
        $response2->assertStatus(400)
            ->assertJson([
                'message' => 'These users are already recommended to each other'
            ]);

        // database columns have the same names as data key names
        // check if the data was inserted into the database
        $this->assertDatabaseHas((new Recommendation())->getTable(), $data);
    }
}
