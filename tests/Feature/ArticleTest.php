<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleAudience;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\Category4Article;
use App\Models\Industry;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use MeiliSearch\Client;
use Tests\AppBaseTestCase;

/**
 * CRUD method testing
 * Class ArticleTest
 * @package Tests\Feature
 */
class ArticleTest extends AppBaseTestCase
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

    private string $endpoint = 'api/v1/article';

    protected function setUp(): void
    {
        parent::setUp();
        // create the index and update the sorting order and filters
        // so that it doesn't return the errors while running tests
        // otherwise, it would return errors like
        //MeiliSearch ApiException: Http Status: 400 - Message: Attribute `__soft_deleted`
        // is not filterable. Available filterable attributes are: ``. - Code: invalid_filter - Type: invalid_request - Link:
        try {
            $articleIndex = (new Article())->searchableAs();
            $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
            $client->createIndex($articleIndex);
            Artisan::call('reset:meilisearch'); // this will add __soft_delete into index column otherwise this test will fail
        } catch (\Exception) {
        }

        Article::factory()
            ->has(ArticleAudience::factory()->count(1), 'audiences')
            ->count(10)->create();
        $command = fn($type) => addslashes("scout:$type App\Models\Article");
        Artisan::call($command('import'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        try {
            $articleIndex = (new Article())->searchableAs();
            $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
            $client->deleteIndex($articleIndex);
        } catch (Exception) {
        }
    }

    /**
     * @throws Exception
     */
    private function data(): array
    {
        return [
            'title' => $this->faker->paragraphs(1, true),
            'description' => $this->faker->paragraphs(2, true),
            'content' => $this->faker->paragraphs(3, true),
            'is_draft' => true,
            'audience' => $this->faker->randomElements(['founder', 'entrepreneur']),
            'publish_after' => now()->format('Y-m-d'),
            'hide_after' => now()->addDays(10)->format('Y-m-d'),
            // put a string of 25 characters to see no error is returned since the string limit is 25
            'tags' => ['hello world', 'hello'],
            'category_ids' => Category4Article::query()->inRandomOrder()->take(5)->get()->pluck('id')->toArray(),
            'industry_ids' => Industry::query()->inRandomOrder()->take(5)->get()->pluck('id')->toArray()
        ];
    }

    public function test_create_validation_error()
    {
        $this->authSuperAdmin();
        $res = $this->post($this->endpoint);
        $res->assertJsonValidationErrors(['title', 'content', 'description', 'audience']);
    }

    /**
     * @throws Exception
     */
    public function test_validate_tags_array()
    {
        // tags must be an array
        $this->authSuperAdmin();
        $data = $this->data();
        $data['tags'] = 'string not an array should return error';
        $res = $this->post($this->endpoint, $data);
        $res->assertJsonValidationErrors(['tags']);
    }

    /**
     * @throws Exception
     */
    public function test_validate_tags_max_limit()
    {
        // each tag in the array can be max 25 characters
        $this->authSuperAdmin();
        $data = $this->data();
        $data['tags'] = [Str::random(26)];
        $res = $this->post($this->endpoint, $data);
        $res->assertJsonValidationErrors(['tags']);
    }

    /**
     * @throws Exception
     */
    public function test_audience_validation_check()
    {
        // audience key has to be an array with founder, entrepreneur or either of the values
        $this->authSuperAdmin();
        $data = $this->data();
        $data['audience'] = 'entrepreneur';
        $res = $this->post($this->endpoint, $data);
        $res->assertJsonValidationErrors(['audience'])
            ->assertJsonPath('errors.audience.0', 'The audience must be an array.');

        $data['audience'] = ['hello'];
        $res2 = $this->post($this->endpoint, $data);
        $res2->assertJsonValidationErrors(['audience'])
            ->assertJsonPath('errors.audience.0', 'Valid values are entrepreneur, founder');
    }

    /**
     * @param TestResponse $res
     * @param array $data
     */
    private function articleTestInResponse(TestResponse $res, array $data)
    {
        $res->assertJson(fn(AssertableJson $json) => $json
            ->where('data.title', Arr::get($data, 'title'))
            ->where('data.content', Arr::get($data, 'content'))
            ->where('data.description', Arr::get($data, 'description'))
            ->has('data.audiences', count(Arr::get($data, 'audience')))
            ->has('data.tags', count(Arr::get($data, 'tags')))
            ->has('data.categories.0.category.name') // check if it's returning an individual category with its name
            ->has('data.industries.0.industry.name')
            ->etc());
    }

    /**
     * @throws Exception
     */
    public function test_create()
    {
        $this->authSuperAdmin();
        $data = $this->data();
        $tags = Arr::get($data, 'tags');
        $res = $this->post($this->endpoint, $data);
        $res->assertCreated();
        $this->articleTestInResponse($res, $data);

        $this->assertDatabaseHas((new Article())->getTable(), ['title' => Arr::get($data, 'title')]);
        $this->assertDatabaseHas((new ArticleTag())->getTable(), ['name' => $tags[0]]);
        $this->assertDatabaseHas((new ArticleAudience())->getTable(), ['audience' => Arr::first($data['audience'])]);
        $this->assertTrue((bool)ArticleCategory::count()); // check if this table has it. If categories was added successfully, then it would have something
    }

    /**
     * @throws Exception
     */
    public function test_edit()
    {
        $this->authSuperAdmin();
        $articleId = Article::query()->first()?->id;
        $data = $this->data();
        $req = $this->put($this->endpoint . '/' . $articleId, $data);
        $req->assertSuccessful();
        $this->articleTestInResponse($req, $data);
        $this->assertDatabaseHas((new Article())->getTable(), ['title' => Arr::get($data, 'title'), 'id' => $articleId]);
    }

    public function test_delete()
    {
        $this->authSuperAdmin();
        $id = Article::first()?->id;
        $url = $this->endpoint . '/' . $id;
        $res = $this->delete($url);
        $res->assertNoContent();

        $get = $this->get($url);
        $get->assertNotFound(); // since deleted, not found

        $deleted = Article::onlyTrashed()->find($id);
        self::assertTrue((bool)$deleted);
    }

    public function test_get_articles()
    {
        $this->authSuperAdmin();
        $this->testPagination($this->endpoint);
    }

    /*public function test_get_article_audience()
    {
        $this->authSuperAdmin();
        $res = $this->get($this->endpoint . '?audience=entrepreneur');
        $res->assertSuccessful();
        $data = Arr::get($res->json(), 'data');
        $ids = Arr::pluck($data, 'id'); // article ids which have entrepreneur in the audiences

        $count = ArticleAudience::query()->where('audience', 'entrepreneur')
            // if all those articles have ent as audience, then the result of this and id count would be the same
            ->whereIn('article_id', $ids)->count();

        $this->assertTrue($count == count($ids));
    }*/

    public function test_forbidden_staff()
    {
        $this->authStaff();
        $this->testForbidden($this->endpoint);
    }
}
