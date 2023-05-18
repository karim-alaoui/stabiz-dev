<?php /** @noinspection ALL */

namespace Tests\Feature;

use App\Models\NewsTopic;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

class NewsTopicTest extends AppBaseTestCase
{
    use WithFaker;

    private string $endpoint = 'api/v1/news-n-topics';
    private array $data;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('reset:meilisearch'); // this will add __soft_delete into index column otherwise this test will fail
    }

    private function staff()
    {
        Staff::factory()->count(10)->create();
        $staff = Staff::factory()->create();
        $staff->assignRole(Staff::SUPER_ADMIN_ROLE);
        Passport::actingAs($staff, guard: 'api-staff');
        return $staff;
    }

    public function test_unauthorized()
    {
        $res = $this->post($this->endpoint);
        $res->assertUnauthorized();

        // works only under api-staff guard, so should not work under any user
        Passport::actingAs(
            User::factory()->create()
        );
        $res = $this->post($this->endpoint);
        $res->assertUnauthorized();
    }

    private function data()
    {
        return [
            'title' => $this->faker->title(),
            'body' => $this->faker->paragraph(),
            'show_after' => now()->addDays(2)->format('Y-m-d'),
            'hide_after' => now()->addDays(20)->format('Y-m-d')
        ];
    }

    public function test_fail_create_news_topic()
    {
        $data = $this->data();

        $this->staff();
        $res = $this->post($this->endpoint);
        $res->assertJsonValidationErrors(['title', 'body']);

        $res2 = $this->post($this->endpoint, array_merge($data, [
            'show_after' => now()->subDays(2)->format('Y-m-d'),
            'hide_after' => now()->subDays(3)->format('Y-m-d')
        ]));

        $res2->assertJsonValidationErrors(['show_after', 'hide_after'])
            ->assertJsonMissingValidationErrors(['title', 'body'])
            ->assertJsonPath('errors.show_after', ["The show after must be a date after or equal to today."])
            ->assertJsonPath('errors.hide_after', ["The hide after must be a date after show after."]);

        $res3 = $this->post($this->endpoint, array_merge($data, [
            'body' => '<p></p>'
        ]));

        $res3->assertJsonValidationErrors(['body'])
            ->assertJsonMissingValidationErrors(['title', 'show_after', 'hide_after']);
    }

    public function test_pass_create_news_topic()
    {
        $data = $this->data();
        $data['visible_to'] = 'others'; // Add the visible_to field with a valid value

        $staff = $this->staff();

        $res = $this->post($this->endpoint, $data);
        $res->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.title', Arr::get($data, 'title'))
                ->etc());

        $table = (new NewsTopic())->getTable();
        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseHas($table, [
            'added_by_staff_id' => $staff->id
        ]);
    }

    public function test_get_news_topic()
    {
        $topic = NewsTopic::factory()->create(['title' => 'unique title']);
        NewsTopic::factory()->count(20)->create();
        // import so that the index is updated and nothing from the previous tests are not included into the index
        Artisan::call('scout:import ' . addslashes('App\Models\NewsTopic'));
        $this->staff();
        $res = $this->get($this->endpoint);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('meta.current_page')
                ->has('meta.total')
                ->has('meta.last_page')
                ->etc());

        $this->assertDatabaseCount((new NewsTopic())->getTable(), 21);

        // check if pagination working perfectly fine
        $paginated = $this->get(sprintf('%s?page=2&per_page=10', $this->endpoint));
        $paginated->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('meta.per_page', (string)10)
                ->etc());
    }

    public function test_get_dates()
    {
        $this->staff();
        $date = now()->subDays(2)->format('Y-m-d');
        NewsTopic::factory()->create([
            'show_after' => $date
        ]);

        NewsTopic::factory()->create([
            'show_after' => now()->subDays(100)->format('Y-m-d')
        ]);

        $res = $this->get(sprintf('%s?show_after=%s', $this->endpoint, $date));
        $res->assertSuccessful();
    }

    public function test_delete()
    {
        $this->staff();
        NewsTopic::factory()->count(20)->create();
        $topic = NewsTopic::factory()->create();
        self::assertTrue(NewsTopic::count() == 21);

        $res = $this->delete(sprintf('%s/%s', $this->endpoint, $topic->id));
        $res->assertStatus(204);

        self::assertTrue((bool)NewsTopic::onlyTrashed()->find($topic->id));
        self::assertTrue(NewsTopic::count() == 20);
    }

    public function test_update_news_topic()
    {
        $topic = NewsTopic::factory()->create();
        $endpoint = sprintf('%s/%s', $this->endpoint, $topic->id);
        $data = [
            'title' => 'title updated',
            'body' => 'body updated'
        ];
        $this->staff();
        $res = $this->put($endpoint, $data);
        $res->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.title', Arr::get($data, 'title'))
                ->where('data.body', Arr::get($data, 'body'))
                ->etc());

        $invaliddates = [
            'show_after' => now()->subDays(2)->format('Y-m-d'),
            'hide_after' => now()->subDays(100)->format('Y-m-d')
        ];

        $res = $this->put($endpoint, $invaliddates);
        $res->assertJsonValidationErrors(['show_after', 'hide_after'])
            ->assertJsonMissingValidationErrors(['title', 'body']);
    }

    public function test_if_forbidden()
    {
        // only super admin can access this. Not anyother staff
        Passport::actingAs(
            Staff::factory()->create(), // creates a normal staff
            guard: $this->staffGuard
        );

        $topic = NewsTopic::factory()->create();
        $topicId = $topic->id;
        $methods = ['post', 'get'];

        $res = $this->post($this->endpoint);
        $res->assertStatus(403);
    }

    /*public function test_news_topic_user_side()
    {
        // This will test if on the user side it's returning
        // news and topic properly
        $count = 10;
        NewsTopic::factory()->count($count)->create();
        $this->assertTrue(NewsTopic::userSide()->count() == $count);// this query gets executed to get the news topic on user side
    }*/
}
