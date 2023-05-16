<?php


namespace Tests;


use App\Models\EntrepreneurProfile;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\AreaSeeder;
use Database\Seeders\AreaSeeder2;
use Database\Seeders\Category4ArticleSeeder;
use Database\Seeders\EducationBackgroundSeeder;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\IncomeRangeSeeder;
use Database\Seeders\IndustrySeeder;
use Database\Seeders\LangLevelSeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\MgmtExpSeeder;
use Database\Seeders\OccupationSeeder;
use Database\Seeders\PackageSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\PresentPostSeeder;
use Database\Seeders\RolesPermissionSeeder;
use Database\Seeders\WorkingStatusSeeder;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Passport;

/**
 * All the tests in the application should extends these
 * class as it has a bunch of helpful methods and seeders
 * and other helpful methods
 * Class AppBaseTestCase
 * @package Tests
 */
class AppBaseTestCase extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected string $staffGuard = 'api-staff';

    /**
     * Authenticate the user super admin while testing
     */
    protected function authSuperAdmin(): Model|Collection|HasApiTokens
    {
        /**@var HasApiTokens $staff */
        $staff = Staff::factory()->create();
        /** @noinspection PhpStaticAsDynamicMethodCallInspection */
        $staff->assignRole(Staff::SUPER_ADMIN_ROLE);
        Passport::actingAs($staff, guard: $this->staffGuard);

        return $staff;
    }

    /**
     * Authenticate as normal staff while testing
     */
    protected function authStaff()
    {
        /**@var HasApiTokens $staff */
        $staff = Staff::factory()->create();
        Passport::actingAs($staff, guard: $this->staffGuard);
    }

    /**
     * @param string $type
     * @return mixed
     */
    protected function authUser(string $type = User::FOUNDER): Model
    {
        /**@var HasApiTokens $user */
        $user = User::factory()->create([
            'type' => $type
        ]);

        Passport::actingAs($user);
        return $user;
    }

    /**
     * @return Model
     */
    protected function authFounder(): Model
    {
        return $this->authUser();
    }

    /**
     * @return Model
     */
    protected function authEntr(): Model
    {
        return $this->authUser(User::ENTR);
    }

    protected $defaultHeaders = [
        'Accept' => 'application/json'
    ];

    /**
     * @throws Exception
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function setUp(): void
    {
        parent::setUp();

        // insert data which are needed in other test
        Artisan::call('migrate');
        Artisan::call('passport:install');
        $this->seed([
            EducationBackgroundSeeder::class,
            WorkingStatusSeeder::class,
            LangLevelSeeder::class,
            PositionSeeder::class,
            PresentPostSeeder::class, // keep it behind position
            IncomeRangeSeeder::class,
            LangSeeder::class,
            RolesPermissionSeeder::class,
            IndustrySeeder::class,
            AreaSeeder::class,
            IndustrySeeder::class,
            OccupationSeeder::class,
            PackageSeeder::class,
            EmailTemplateSeeder::class,
            Category4ArticleSeeder::class,
            MgmtExpSeeder::class,
            AreaSeeder2::class
        ]);

        User::factory()
            ->has(EntrepreneurProfile::factory()->count(1), 'entrProfile')
            ->count(5)
            ->create(['type' => User::ENTR]);

        // create some basic staff
        Staff::factory()->count(10)->create();

        // create a super admin
        $superadmin = Staff::factory()->create();
        $superadmin->assignRole(Staff::SUPER_ADMIN_ROLE);
    }

    /**
     * Testing pagination
     */
    protected function testPagination($endpoint)
    {
        $res = $this->get($endpoint);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('meta.per_page')
                ->has('meta.current_page')
                ->has('meta.last_page')
                ->has('meta.total')
                ->etc());
    }

    /**
     * @param string $endpoint
     * @param string $method
     */
    protected function testForbidden(string $endpoint, string $method = 'get')
    {
        $method = strtolower($method);
        $res = match ($method) {
            'get' => $this->get($endpoint),
            'post' => $this->post($endpoint),
            'put' => $this->put($endpoint),
            'patch' => $this->patch($endpoint),
        };

        $res->assertForbidden();
    }

    /**
     * @return mixed
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function createEntr(): mixed
    {
        return User::factory()->entrepreneur()->create();
    }

    /**
     * @return mixed
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function createFdr(): mixed
    {
        return User::factory()->founder()->create();
    }

    /**
     * When you search something make sure that it returns
     * the expected filtered result
     * @param string $endpoint
     * @param array $verifyValues - verify that in the result these values exists. Eg - [1,2,3] would be all the results must have either of these value for the result key
     * @param string $resultKey - in the result of collection, in each object what key value to check Eg - id would be checking the id value of the each object of the result collection
     */
    protected function searchWorking(string $endpoint, array $verifyValues, string $resultKey = 'id')
    {
        $req = $this->get($endpoint);
        $req->assertSuccessful();

        $results = Arr::get($req->json(), 'data', []);
        if (count($results)) {
            // the search result must have these results
            collect($results)->map(fn($result) => $this->assertTrue(in_array(Arr::get($result, $resultKey), $verifyValues)));
        }
    }

    /**
     * Add query to the endpoint
     * @param string $endpoint
     * @param array $query
     * @return string
     */
    protected function queryEndpoint(string $endpoint, array $query = []): string
    {
        return sprintf('%s?%s', $endpoint, http_build_query($query));
    }

    /**
     * Pluck a key from a query result and make an array of it
     * @param Builder $query
     * @param string $pluckKey
     * @return array
     */
    protected function pluckVal(Builder $query, string $pluckKey = 'id'): array
    {
        return $query->get()->pluck($pluckKey)->toArray();
    }

    protected function tearDown(): void
    {
//        $client = new Client(Config::get('scout.meilisearch.host'), Config::get('scout.meilisearch.key'));
        try {
            /*$index1 = (new NewsTopic())->searchableAs();
            $index2 = (new Article())->searchableAs();
            $client->index($index1)->delete();
            $client->index($index2)->delete();*/
        } catch (\Exception) {
        }
        parent::tearDown(); // keep the teardown here don't put it at the top of this method. that won't work
    }
}
