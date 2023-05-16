<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\FounderProfile;
use App\Models\IncomeRange;
use App\Models\Industry;
use App\Models\Position;
use App\Models\Prefecture;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

/**
 * Update founder test
 * A founder can be updated by the founder himself and the super admin
 * Class UpdateFdrTest
 * @package Tests\Feature
 */
class UpdateFdrTest extends AppBaseTestCase
{
    use WithFaker;

    private string $endpoint = '/api/v1/user/fdr';

    /**
     * Authenticate as founder
     */
    private function authFdr()
    {
        $user = User::factory()
            ->create([
                'type' => User::FOUNDER
            ]);
        $user->fdrProfile()->save(FounderProfile::factory()->make());

        Passport::actingAs($user);
    }

    public function test_validation_error_work_date()
    {
        $data = [
            // this data should always be greater than yesterday. Either today or greater than today
            'work_start_date_4_entr' => now()->subDay()->format('Y-m-d')
        ];
        $this->authSuperAdmin();
        $userId = $this->createFdr()->id;
        $res = $this->put($this->endpoint . ($userId ? '/' . $userId : ''), $data);
        $res->assertJsonValidationErrors(['work_start_date_4_entr']);
        $res->assertJson(fn(AssertableJson $json) => $json
            ->where('errors.work_start_date_4_entr.0', 'The work start date 4 entr must be a date after yesterday.')
            ->etc());
    }

    /**
     * @return array
     */
    private function data()
    {
        $gender = $this->faker->randomElement(['male', 'female']);
        $company = fn() => $this->faker->company();
        $industries = array_slice($this->faker->randomElements(Industry::all()->pluck('id')->toArray()), 0, 3);
        return [
            'first_name' => $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName($gender),
            'first_name_cana' => $this->faker('ja')->firstName('male'),
            'last_name_cana' => $this->faker('ja')->lastName('male'),
            'gender' => $gender,
            'income_range_id' => $this->faker->randomElement(IncomeRange::all()->pluck('id')->toArray()),
            'dob' => $this->faker->dateTimeBetween(endDate: now()->subYears(20)->format('Y-m-d'))->format('Y-m-d'),
            'company_name' => $this->faker->company(),
            'is_listed_company' => $this->faker->randomElement([true, false]),
            'area_id' => $this->faker->randomElement(Area::all()->pluck('id')->toArray()),
            'prefecture_id' => $this->faker->randomElement(Prefecture::all()->pluck('id')->toArray()),
            'no_of_employees' => 200,
            'capital' => 324927,
            'last_year_sales' => 198738,
            'established_on' => '1995-10-01',
            'business_partner_company' => $this->faker->company(),
            'major_bank' => $this->faker->company(),
            'company_features' => implode(' <br/>', $this->faker->paragraphs()),
            'job_description' => implode(' <br/>', $this->faker->paragraphs()),
            'application_conditions' => implode(' <br/>', $this->faker->paragraphs()),
            'employee_benefits' => implode(' <br/>', $this->faker->paragraphs()),
            'company_industry_ids' => $industries,
            'affiliated_companies' => [$company(), $company(), $company()],
            'major_stock_holders' => [$company(), $company(), $company()],
            'pfd_industry_ids' => $industries,
            'pfd_prefecture_ids' => Prefecture::query()->take(2)->get()->pluck('id')->toArray(),
            'pfd_position_ids' => $this->faker->randomElements(Position::all()->pluck('id')->toArray(), 3),
            'offered_income_range_id' => IncomeRange::first()->id,
            'work_start_date_4_entr' => now()->addDay()->format('Y-m-d')
        ];
    }

    /**
     * @param null $userId
     */
    private function checkUpdate($userId = null)
    {
        $data = $this->data();
        $res = $this->put($this->endpoint . ($userId ? '/' . $userId : ''), $data);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.first_name', Arr::get($data, 'first_name'))
                ->where('data.last_name', Arr::get($data, 'last_name'))
                ->where('data.first_name_cana', Arr::get($data, 'first_name_cana'))
                ->where('data.last_name_cana', Arr::get($data, 'last_name_cana'))
                ->where('data.gender', Arr::get($data, 'gender'))
                ->where('data.dob', Arr::get($data, 'dob'))
                ->where('data.type', User::FOUNDER)
                ->where('data.founder_profile.company_name', Arr::get($data, 'company_name'))
                ->where('data.founder_profile.no_of_employees', Arr::get($data, 'no_of_employees'))
                ->where('data.founder_profile.capital', Arr::get($data, 'capital'))
                ->where('data.founder_profile.last_year_sales', Arr::get($data, 'last_year_sales'))
                ->where('data.founder_profile.established_on', Arr::get($data, 'established_on'))
                ->where('data.founder_profile.business_partner_company', Arr::get($data, 'business_partner_company'))
                ->where('data.founder_profile.major_bank', Arr::get($data, 'major_bank'))
                ->where('data.founder_profile.company_features', Arr::get($data, 'company_features'))
                ->where('data.founder_profile.job_description', Arr::get($data, 'job_description'))
                ->where('data.founder_profile.application_conditions', Arr::get($data, 'application_conditions'))
                ->where('data.founder_profile.employee_benefits', Arr::get($data, 'employee_benefits'))
                ->where('data.founder_profile.area.id', Arr::get($data, 'area_id'))
                ->where('data.founder_profile.prefecture.id', Arr::get($data, 'prefecture_id'))
                ->has('data.founder_profile.company_industries', count(Arr::get($data, 'company_industry_ids')))
                ->has('data.founder_profile.affiliated_companies', count(Arr::get($data, 'affiliated_companies')))
                ->has('data.founder_profile.major_stock_holders', count(Arr::get($data, 'major_stock_holders')))
                ->has('data.founder_profile.pfd_prefectures', count(Arr::get($data, 'pfd_prefecture_ids')))
                ->has('data.founder_profile.pfd_positions', count(Arr::get($data, 'pfd_position_ids')))
                ->where('data.founder_profile.offered_income.id', Arr::get($data, 'offered_income_range_id'))
                ->where('data.founder_profile.work_start_date_4_entr', Arr::get($data, 'work_start_date_4_entr'))
                ->etc());
    }

    public function test_if_updated()
    {
        $this->authFdr();
        $this->checkUpdate();
    }

    public function test_if_updated_superadmin()
    {
        $user = $this->createFdr();
        $this->authSuperAdmin();
        $this->checkUpdate($user->id);
    }

    public function test_if_unauthorized_when_user()
    {
        // the endpoint which is used by the staff to update an user
        // if that endpoint is hit by any user, it should return 401
        $user = $this->createFdr();
        $this->authUser();
        $res = $this->put($this->endpoint . '/' . $user->id);
        $res->assertUnauthorized();
    }

    public function test_with_entr()
    {
        // test it with entrepreneur and see if it returns forbidden
        $user = User::factory()->entrepreneur()->create();
        Passport::actingAs($user);
        $res = $this->put($this->endpoint);
        $res->assertForbidden();
    }

    public function test_if_updated_by_staff()
    {
        // any staff should be able to update any user
        $this->authStaff();
        $user = $this->createFdr();
        $url = $this->endpoint . '/' . $user->id; // this endpoint is used to update from the admin side
        $res = $this->put($url);
        $res->assertSuccessful();
    }
}
