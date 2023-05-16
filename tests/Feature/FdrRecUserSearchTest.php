<?php

namespace Tests\Feature;

use App\Models\IncomeRange;
use App\Models\Industry;
use App\Models\MgmtExp;
use App\Models\Occupation;
use App\Models\Position;
use App\Models\Prefecture;
use App\Models\PresentPost;
use App\Models\User;
use App\Models\WorkingStatus;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

/**
 * Search users that the staff would recommend
 */
class FdrRecUserSearchTest extends AppBaseTestCase
{
    private string $endpoint = 'api/v1/rec-users-search';

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->state([
            'type' => User::FOUNDER
        ])->create();
        User::factory()->state([
            'type' => User::FOUNDER
        ])
            ->count(5)
            ->create();
        User::factory()->state([
            'type' => User::ENTR
        ])
            ->count(5)
            ->create();
        $this->endpoint = sprintf('%s?user_id=%s', $this->endpoint, $user->id);
    }

    public function test_if_user_can_access()
    {
        // user should not be able to access
        // as this can only be accessed by the staff
        $this->authUser();
        $req = $this->get($this->endpoint);
        $req->assertStatus(401);
    }

    /**
     * Provide this data to the request
     * The purpose of providing this data is not to test if it's returning any data or not
     * Sometimes due to some spelling mistake you misspell the name of database column.
     * If we provide query for all the query params, it will at least search the database for all those columns.
     * If there's any error due to spelling mistake, it would come out
     * @return array
     */
    private function queryData(): array
    {
        return [
            'name' => Str::random(),
            'age_greater_than' => 18,
            'age_lower_than' => 40,
            'gender' => 'male',
            'school_name' => Str::random(),
            'school_major' => Str::random(),
            'present_company' => Str::random(),
            'working_status_id' => WorkingStatus::first()->id,
            'occupation_id' => Occupation::first()->id,
            'present_post_id' => PresentPost::first()->id,
            'transfer' => 'yes',
            'management_exp_id' => MgmtExp::first()->id,
            'pfd_industry_ids' => implode(',', Industry::query()->take(3)->get()->pluck('id')->toArray()),
            'pfd_positions_ids' => implode(',', Position::query()->take(3)->get()->pluck('id')->toArray()),
            'pfd_prefecture_ids' => implode(',', Prefecture::query()->take(3)->get()->pluck('id')->toArray()),
            'expected_income_range_id' => IncomeRange::query()->inRandomOrder()->first()->id,
        ];
    }

    public function test_if_working_fine()
    {
        $this->authStaff();
        $endpoint = sprintf('%s&%s', $this->endpoint, http_build_query($this->queryData()));
        $req = $this->get($endpoint);
        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data')
                ->etc());
    }

    public function test_if_returning_results()
    {
        $this->authStaff();
        $req = $this->get($this->endpoint);
        $req->assertSuccessful();
        $data = $req->json()['data'];
        $this->assertNotEmpty($data);
        // since the user that is sent in the query is a founder
        // check if all the users that are returned are entrepreneur or not
        array_map(fn($user) => $this->assertTrue($user['type'] == User::ENTR), $data);
    }

    public function test_if_returning_results_for_entr()
    {
        $this->authStaff();
        $entr = User::factory()->state(['type' => User::ENTR])->create();
        $endpoint = 'api/v1/rec-users-search?user_id=' . $entr->id;

        $req = $this->get($endpoint);
        $data = $req->json()['data'];
        $this->assertNotEmpty($data);
        $req->assertSuccessful();

        // since the user that is sent in the query is an entrepreneur
        // check if all the users that are returned are founder or not
        array_map(fn($user) => $this->assertTrue($user['type'] == User::FOUNDER), $data);
    }

    /**
     * Test if search on query on age_greater_than is working fine
     * There was problem as the front end devs said before. thus
     * putting this test
     */
    public function test_age_query_working_fine()
    {
        $this->authStaff();
        $fdr = User::factory()->founder()->create();
        $ageGreaterThan = 40;
        $endpoint = sprintf('api/v1/rec-users-search?user_id=%s&age_greater_than=%s', $fdr->id, $ageGreaterThan);

        $req = $this->get($endpoint);
        $data = $req->json('data');
        $req->assertSuccessful();

        // check that all the users that are returned are entrepreneur since we're searching for founder user here
        array_map(fn($user) => $this->assertTrue($user['type'] == User::ENTR), $data);
        // get the user ids which were returned
        $userIds = Arr::pluck($data, 'id');
        // fetch those user ids from the database and filter their ids
        $ageGreaterUserIds = User::whereIn('id', $userIds)
            ->where([
                [DB::raw("date_part('year', age(now(), dob))"), '>=', $ageGreaterThan]
            ])
            ->get()
            ->pluck('id')
            ->toArray();

        // if the search worked, then both the results should be the same
        $this->assertTrue($userIds === $ageGreaterUserIds);
    }
}
