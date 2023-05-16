<?php /** @noinspection PhpPossiblePolymorphicInvocationInspection */

/** @noinspection PhpMissingDocCommentInspection */

namespace Tests\Feature;

use App\Models\Area;
use App\Models\EducationBackground;
use App\Models\IncomeRange;
use App\Models\Industry;
use App\Models\LangLevel;
use App\Models\Language;
use App\Models\MgmtExp;
use App\Models\Occupation;
use App\Models\Position;
use App\Models\Prefecture;
use App\Models\PresentPost;
use App\Models\User;
use App\Models\WorkingStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

/**
 * Update tests related to an user who's an entrepreneur
 * Update of an entrepreneur can be done by the entrepreneur himself or the super admin
 */
class EntrUserUpdateTest extends AppBaseTestCase
{
    use WithFaker;
    use RefreshDatabase;

    private string $endpoint = 'api/v1/user/entrepreneur';

    private function user()
    {
        $user = User::factory()
            ->entrepreneur()
            ->create();
        Passport::actingAs($user);
        return $user;
    }

    /**
     * Common method to call api
     * @param array $data
     * @return TestResponse
     */
    private function apiCall(array $data = []): TestResponse
    {
        return $this->put($this->endpoint, $data);
    }

    public function test_if_method_is_put()
    {
        $res = $this->post($this->endpoint);
        $res2 = $this->get($this->endpoint);
        $res->assertStatus(405);
        $res2->assertStatus(405);
    }

    public function test_if_forbidden_if_user_is_founder()
    {
        Passport::actingAs(
            User::factory()->founder()->create()
        );

        $res = $this->apiCall();
        $res->assertStatus(403);
    }

    /**
     * If the user selects other as present post,
     * present_post_other becomes required field
     */
    /*public function test_if_other_post_required()
    {
        $this->user();
        $otherPostId = PresentPost::where('name', 'other')->first()->id;
        $call = $this->apiCall([
            'present_post_id' => $otherPostId
        ]);

        $call->assertStatus(422);
        $call->assertJsonPath('errors.present_post_other', [
            'Please type other post since you selected other as present post'
        ]);
    }*/

    /**
     * if the user selected other from the language dropdown,
     * then lang_other field should be required
     */
    public function test_if_lang_other_required()
    {
        $this->user();
        $otherlangid = Language::where('name', 'other')->first()->id;
        $call = $this->apiCall(['lang_id' => $otherlangid]);
        $call->assertStatus(422);
        $call->assertJsonPath('errors.lang_other', [
            'Please type other language since you selected other as language'
        ]);
    }

    /**
     * Test those fields which are dependent on database fields
     */
    public function test_field_dependent_on_database()
    {
        $this->user();
        // provide some random value which could never exist in the database and check if returning validation error
        $data = [
            'education_background_id' => 0,
            'working_status_id' => 0,
            'present_post_id' => 0,
            'occupation_id' => 0,
            'lang_id' => 0,
            'lang_level_id' => 0,
            'en_lang_level_id' => 0,
            'expected_income_range_id' => 0
        ];
        $call = $this->apiCall($data);
        $call->assertJsonValidationErrors(array_keys($data));
    }


    public function test_for_enum_field_validation()
    {
        $this->user();
        $random = Str::random();
        $call = $this->apiCall([
            'gender' => $random,
            'transfer' => $random
        ]);

        $call->assertStatus(422);
        $call->assertJson(fn(AssertableJson $json) => $json
            ->hasAll([
                'errors.gender',
                'errors.transfer'
            ])
            ->etc());
    }

    public function test_age()
    {
        $now = now()->subYears(17)->format('Y/m/d');
        $this->user();
        $call = $this->apiCall(['dob' => $now]);
        $call->assertStatus(422);
        $call->assertJsonPath('errors.dob', [
            'Age(dob) must be 18 years or above'
        ]);
    }

    /**
     * Columns the belong to users table
     * if they are getting updated or not
     */
    public function test_if_user_column_updated()
    {
        $age = now()->subYears(30)->format('Y-m-d');
        $user = $this->user();
        $gender = $user->gender == 'male' ? 'female' : 'male';
        $data = [
            'gender' => $gender,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'dob' => $age
        ];

        $call = $this->apiCall($data);
        $call->assertStatus(200);
        $call->assertJson(fn(AssertableJson $json) => $json
            ->where('data.first_name', Arr::get($data, 'first_name'))
            ->where('data.last_name', Arr::get($data, 'last_name'))
            ->where('data.gender', $gender)
            ->where('data.dob', $age)
            ->etc());
    }

    /**
     * Check if the values that belong to ent profile table
     * if they are getting updated
     */
    public function test_if_ent_profile_updated()
    {
        $langLevelId = LangLevel::query()->inRandomOrder()->first()->id;
        $this->user();
        $data = [
            'address' => $this->faker()->address(),
            'education_background_id' => EducationBackground::first()->id,
            'school_name' => Str::random(),
            'working_status_id' => WorkingStatus::first()->id,
            'present_company' => Str::random(),
            'present_post_id' => PresentPost::first()->id,
            'occupation_id' => Occupation::first()->id,
            'lang_id' => Language::first()->id,
            'lang_level_id' => $langLevelId,
            // keep the en level different than native lang level
            // because both of these columns are foreign ids to from lang level table
            'en_lang_level_id' => LangLevel::query()->inRandomOrder()->where('id', '!=', $langLevelId)->first()->id,
            'transfer' => 'yes',
            'expected_income_range_id' => IncomeRange::query()->inRandomOrder()->first()->id,
            'prefecture_id' => Prefecture::query()->inRandomOrder()->first()->id,
            'area_id' => Area::query()->inRandomOrder()->first()->id,
            'income_range_id' => IncomeRange::query()->inRandomOrder()->first()->id,
            'work_start_date' => now()->addDay()->format('Y-m-d'),
            'school_major' => Str::random(),
            'management_exp_id' => MgmtExp::query()->inRandomOrder()->first()->id,
        ];

        $call = $this->apiCall($data);
        $call->assertStatus(200);
        $call->assertJson(fn(AssertableJson $json) => $json
            ->where('data.entrepreneur_profile.address', Arr::get($data, 'address'))
            ->where('data.entrepreneur_profile.education_background.id', Arr::get($data, 'education_background_id'))
            ->where('data.entrepreneur_profile.school_name', Arr::get($data, 'school_name'))
            ->where('data.entrepreneur_profile.working_status.id', Arr::get($data, 'working_status_id'))
            ->where('data.entrepreneur_profile.present_company', Arr::get($data, 'present_company'))
            ->where('data.entrepreneur_profile.present_post.id', Arr::get($data, 'present_post_id'))
            ->where('data.entrepreneur_profile.occupation.id', Arr::get($data, 'occupation_id'))
            ->where('data.entrepreneur_profile.lang.id', Arr::get($data, 'lang_id'))
            ->where('data.entrepreneur_profile.lang_ability.id', Arr::get($data, 'lang_level_id'))
            ->where('data.entrepreneur_profile.en_lang_ability.id', Arr::get($data, 'en_lang_level_id'))
            ->where('data.entrepreneur_profile.expected_income.id', Arr::get($data, 'expected_income_range_id'))
            ->where('data.entrepreneur_profile.area.id', Arr::get($data, 'area_id'))
            ->where('data.entrepreneur_profile.prefecture.id', Arr::get($data, 'prefecture_id'))
            ->where('data.income.id', Arr::get($data, 'income_range_id'))
            ->where('data.entrepreneur_profile.work_start_date', Arr::get($data, 'work_start_date'))
            ->where('data.entrepreneur_profile.school_major', Arr::get($data, 'school_major'))
//            ->where('data.entrepreneur_profile.management_exp.id', Arr::get($data, 'management_exp_id'))
            ->etc());
    }

    public function test_if_updated_by_staff()
    {
        // any staff can update any user
        $user = $this->createEntr();
        $url = $this->endpoint . '/' . $user->id;
        $this->authStaff();
        $res = $this->put($url);
        $res->assertSuccessful();
    }

    public function test_forbidden_by_fdr()
    {
        // fdr user should not be able to call the endpoint
        $this->authFounder();
        $req = $this->put($this->endpoint);
        $req->assertForbidden();
    }

    public function test_unauthorized()
    {
        $user = $this->user();
        // if a normal user try to access this uri it should return 401
        $res = $this->put($this->endpoint . '/' . $user->id);
        $res->assertUnauthorized();
    }

    public function test_validation_work_start_date()
    {
        $data = [
            // this should be always greater than yesterday which means either today or greater than today
            'work_start_date' => now()->subDay()->format('Y-m-d')
        ];
        $this->authEntr();
        $req = $this->put($this->endpoint, $data);
        $req->assertJsonValidationErrors(['work_start_date']);
        $req->assertJson(fn(AssertableJson $json) => $json
            ->where('errors.work_start_date.0', 'Work start date has to be after yesterday')
            ->etc()
        );
    }

    public function test_update_multiple_value_fields()
    {
        // the fields that take multiple values
        // check when updating them, if it's returning those values or not
        $this->authEntr();
        $industries = fn() => Industry::query()->inRandomOrder()->take(3)->get()->pluck('id')->toArray();
        $data = [
            'industry_ids' => $industries(),
            'pfd_industry_ids' => $industries(),
            'pfd_prefecture_ids' => Prefecture::query()->inRandomOrder()->take(3)->get()->pluck('id')->toArray(),
            'pfd_position_ids' => Position::query()->inRandomOrder()->take(3)->get()->pluck('id')->toArray()
        ];

        $req = $this->put($this->endpoint, $data);
        $req->assertSuccessful();

        $profileData = Arr::get($req->json(), 'data.entrepreneur_profile', []);

        $industriesExp = Arr::get($profileData, 'industries_exp');
        $industriesExp = Arr::pluck($industriesExp, 'id');
        $this->assertEquals(asort($industriesExp), asort($data['industry_ids']));

        $pfdIndustries = Arr::get($profileData, 'industries_pfd');
        $pfdIndustries = Arr::pluck($pfdIndustries, 'id');
        $this->assertEquals(asort($pfdIndustries), asort($data['pfd_industry_ids']));

        $prefectures = Arr::get($profileData, 'prefectures_pfd');
        $prefectures = Arr::pluck($prefectures, 'id');
        $this->assertEquals(asort($prefectures), asort($data['pfd_prefecture_ids']));

        $positions = Arr::get($profileData, 'positions_pfd');
        $positions = Arr::pluck($positions, 'id');
        self::assertEquals(asort($positions), asort($data['pfd_position_ids']));
    }
}
