<?php

namespace Tests\Feature;

use App\Models\EntrepreneurProfile;
use App\Models\User;
use Illuminate\Support\Arr;
use Tests\AppBaseTestCase;

/**
 * Class SearchEntrTest
 * @package Tests\Feature
 */
class SearchEntrTest extends AppBaseTestCase
{
    /**
     * @param array $query
     * @return string
     */
    public function endpoint(array $query = []): string
    {
        return sprintf('/api/v1/search-entr?%s', http_build_query($query));
    }

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()
            ->has(EntrepreneurProfile::factory()->count(1), 'entrProfile')
            ->count(10)
            ->create(['type' => User::ENTR]);
    }

    public function test_if_forbidden_by_entr()
    {
        $this->authEntr();
        $req = $this->get($this->endpoint());
        $req->assertForbidden();
    }

    public function test_pagination()
    {
        $this->authFounder();
        $this->testPagination($this->endpoint());
    }

    /**
     * Search with individual query
     * And see if it the search is working
     * @param string $queryKey
     * @param string $dbCol
     */
    private function searchWorkingFine(string $queryKey, string $dbCol)
    {
        $query = [
            $queryKey => EntrepreneurProfile::whereNotNull($dbCol)->first()?->{$queryKey}
        ];
        $this->authFounder();
        $val = Arr::get($query, $queryKey);
        if (!$val) return;
        $userQ = EntrepreneurProfile::where($dbCol, $val); // check which users have that value
        $userIds = $this->pluckVal($userQ, 'user_id');  // get the array of those users' id
        $this->searchWorking($this->endpoint($query), $userIds); // search in the search result if only those user ids are there
    }

    public function test_search_working()
    {
        $queryKeys = [
            'school_name' => 'school_name', // queryKey => dbColumn for the query key
            'working_status_id' => 'working_status_id',
            'present_post_id' => 'present_post_id',
            'occupation_id' => 'occupation_id',
            'eng_lang_level' => 'en_lang_level_id',
            'lang_ability' => 'lang_level_id',
            'education_background_id' => 'education_background_id'
        ];

        foreach ($queryKeys as $queryKey => $dbCol) {
            $this->searchWorkingFine($queryKey, $dbCol);
        }
    }
}
