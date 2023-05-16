<?php

namespace Tests\Feature;

use App\Models\FdrPfdIndustry;
use App\Models\FdrPfdPrefecture;
use App\Models\IncomeRange;
use App\Models\User;
use Database\Seeders\FounderSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Tests\AppBaseTestCase;

/**
 * Class SearchFdrTest
 * @package Tests\Feature
 */
class SearchFdrTest extends AppBaseTestCase
{
    private string $url = '/api/v1/search-fdr';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FounderSeeder::class);
    }

    public function test_if_forbidden()
    {
        // should be forbidden if the user is founder
        // only entr can search founders
        $this->authFounder();
        $res = $this->get($this->url);
        $res->assertForbidden();
    }

    public function test_right_result()
    {
        $this->seed(FounderSeeder::class);
        $this->authEntr();
        // the result should not return any unnecessary data
        // should only return certain data
        $req = $this->get($this->url);
        $user = User::founders()->with('fdrProfile')->first();
        $req->assertSuccessful()
            // should not expose anymore data than this in the API
            ->assertJsonPath('data.0', [
                'id' => $user->id,
                'fdr_profile' => [
                    'user_id' => $user->id,
                    'company_name' => $user?->fdrProfile?->company_name
                ]
            ]);
    }

    public function test_pagination()
    {
        $this->authEntr();
        $this->seed(FounderSeeder::class);
        $this->testPagination($this->url);
    }

    /**
     * Check when you search for something, if it only returns those users who match those values
     * @param array $endpoint
     * @param array $verifyValues
     */
    /*public function searchWorking(array $endpoint, array $verifyValues)
    {
        $this->authEntr();

        $endpoint = sprintf('%s?%s', $this->url, http_build_query($endpoint));
        $req = $this->get($endpoint);
        $req->assertSuccessful();

        $results = Arr::get($req->json(), 'data', []);
        if (count($results)) {
            // the search result must have these user ids
            collect($results)->map(fn($result) => $this->assertTrue(in_array($result['id'], $verifyValues)));
        }
    }*/

    /**
     * User query
     * @param Builder $query
     * @return array
     */
    private function getUserIds(Builder $query): array
    {
        return $query
            ->get()
            ->pluck('id')
            ->toArray();
    }

    public function test_if_prefecture_search_working()
    {
        $this->authEntr();
        $search = [
            'prefecture_id' => FdrPfdPrefecture::first()->prefecture_id
        ];

        // the founder users who actually have those prefectures as pfd
        $users = User::query()->whereHas('fdrProfile.pfdPrefectures', function ($q) use ($search) {
            $q->where('prefecture_id', Arr::get($search, 'prefecture_id'));
        });

        $this->searchWorking($this->queryEndpoint($this->url, $search), $this->getUserIds($users));
    }

    public function test_if_area_search_working()
    {
        $this->authEntr();
        $search = [
            'area_id' => FdrPfdPrefecture::with('prefecture')->first()->prefecture->area_id
        ];

        $users = User::query()->whereHas('fdrProfile.pfdPrefectures.prefecture', function ($q) use ($search) {
            $q->where('area_id', Arr::get($search, 'area_id'));
        });

        $this->searchWorking($this->queryEndpoint($this->url, $search), $this->getUserIds($users));
    }

    public function test_if_industry_working_fine()
    {
        $this->authEntr();
        $search = [
            'industries' => FdrPfdIndustry::query()->first()->industry_id
        ];

        $users = User::query()
            ->whereHas('fdrProfile.pfdIndustries', function (Builder $q) use ($search) {
                $q->where('industry_id', Arr::get($search, 'industries'));
            }) // also search the company industries of the founder
            ->orWhereHas('fdrProfile.companyIndustries', function (Builder $q) use ($search) {
                $q->where('industry_id', Arr::get($search, 'industries'));
            });
        $this->searchWorking($this->queryEndpoint($this->url, $search), $this->getUserIds($users));
    }

    public function test_if_expected_income_working()
    {
        $this->authEntr();
        $search = [
            'expecting_income' => IncomeRange::query()->inRandomOrder()->first()->id
        ];
        $users = User::query()
            ->whereHas('fdrProfile', function (Builder $q) use ($search) {
                $q->where('offered_income_range_id', Arr::get($search, 'expecting_income'));
            });

        $this->searchWorking($this->queryEndpoint($this->url, $search), $this->getUserIds($users));
    }
}
