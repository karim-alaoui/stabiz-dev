<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

/**
 * Class HandleApplTest
 * @package Tests\Feature
 */
class HandleApplTest extends AppBaseTestCase
{
    use WithFaker;

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
        $factory = Application::factory()
            ->for(User::factory()->state(['type' => User::ENTR]), 'appliedTo')
            ->for(User::factory()->state(['type' => User::FOUNDER]), 'appliedBy')
            ->count(10);

        $factory->create();
        $factory->create(['accepted_at' => now()]);
        $factory->create(['rejected_at' => now()]);
    }

    public function test_forbidden_if_not_applied_to_the_user()
    {
        $user = $this->authFounder();
        // one can only accept or reject an application if it was applied to the user
        $apl = Application::query()->where('applied_to_user_id', '!=', $user->id)->first();
        $endpoint = fn($type) => 'api/v1/applications/' . $type . '/' . $apl->id;
        $reject = $this->post($endpoint('reject'));
        $reject->assertForbidden();

        $accept = $this->post($endpoint('accept'));
        $accept->assertForbidden();
    }

    public function test_accepted()
    {
        $apl = Application::notResponded()->first();
        Passport::actingAs($apl->appliedTo);

        $accept = $this->post('api/v1/applications/accept/' . $apl->id);
        $accept->assertSuccessful();

        // check that the column is updated
        $this->assertTrue((bool)Application::where('id', $apl->id)->whereNotNull('accepted_at')->first());
    }

    public function test_rejected()
    {
        $apl = Application::notResponded()->first();
        Passport::actingAs($apl->appliedTo);

        $reject = $this->post('api/v1/applications/accept/' . $apl->id);
        $reject->assertSuccessful();

        $this->assertTrue((bool)Application::whereNotNull('accepted_at')->where('id', $apl->id)->first());
    }

    /**
     * @param $type
     * @param $count
     * @param array $query
     */
    private function getApplication($type, $count, array $query = [])
    {
        $req = $this->get('api/v1/applications/' . $type . '?per_page=100&' . (http_build_query($query)));
        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data', $count)
                ->etc());
    }

    public function test_get_applied()
    {
        $apl = Application::first();
        Passport::actingAs($apl->appliedBy);
        $count = Application::where('applied_by_user_id', $apl->appliedBy->id)->count();

        $this->getApplication('applied', $count);
    }

    public function test_get_recvd_application()
    {
        $apl = Application::first();
        $user = $apl->appliedTo;
        Passport::actingAs($user);
        $count = Application::where('applied_to_user_id', $user->id)->count();

        $this->getApplication('recvd', $count);
    }

    public function test_get_applied_filter_rejected()
    {
        // rejected applications
        $apl = Application::rejected()->first();
        Passport::actingAs($apl->appliedBy);

        $req = $this->get('api/v1/applications/applied?type=rejected');
        $req->assertSuccessful();
        /*->assertJson(fn(AssertableJson $json) => $json
            ->first(fn(AssertableJson $first) => $first
                ->where('rejected_at')
                ->etc())
            ->etc());*/
    }
}
