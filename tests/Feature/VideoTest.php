<?php

namespace Tests\Feature;

use App\Models\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

/**
 * Class VideoTest
 * @package Tests\Feature
 */
class VideoTest extends AppBaseTestCase
{
    use WithFaker;

    private string $endpoint = 'api/v1/videos';

    public function test_get_videos()
    {
        $staff = $this->authSuperAdmin();
        Video::factory()->count(10)->create(['staff_id' => $staff->id]);
        $res = $this->get($this->endpoint);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.0.title') // check it's returning the data
                ->has('data', 10)
                ->etc());

        $this->testPagination($this->endpoint);
    }

    public function test_forbidden_by_staff()
    {
        $this->authStaff();
        $res = $this->get($this->endpoint);
        $res->assertForbidden();
    }

    public function test_create_video_validation_errors()
    {
        $this->authSuperAdmin();
        $res = $this->post($this->endpoint);
        $res->assertJsonValidationErrors(['title', 'link', 'description']);
    }

    private function data(): array
    {
        return [
            'title' => Str::random(),
            'description' => Str::random(),
            'link' => 'https://youtube.com'
        ];
    }

    public function test_youtube_link_validation()
    {
        $this->authSuperAdmin();
        $data = $this->data();
        $data['link'] = 'https://google.com';

        $res = $this->post($this->endpoint, $data);
        $res->assertJsonValidationErrors(['link'])
            ->assertJsonPath('errors.link', ['link has to be youtube link']);
    }

    public function test_create_success()
    {
        $this->authSuperAdmin();
        $data = $this->data();
        $res = $this->post($this->endpoint, $data);
        $res->assertCreated()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.title', Arr::get($data, 'title'))
                ->etc());
    }

    public function test_get_video()
    {
        $this->authSuperAdmin();
        $video = Video::factory()->create();
        $res = $this->get($this->endpoint . '/' . $video->id);
        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.title', $video->title) // verify that returning the same video
                ->etc());
    }

    public function test_update_video()
    {
        $this->authSuperAdmin();
        $video = Video::factory()->create();
        $res = $this->put($this->endpoint . '/' . $video->id, ['title' => 'india']);
        $res->assertSuccessful()
            ->assertJsonPath('data.title', 'india');
    }

    public function test_del_video()
    {
        $this->authSuperAdmin();
        $video = Video::factory()->create();
        $count = Video::withTrashed()->count();
        $res = $this->delete($this->endpoint . '/' . $video->id);
        $res->assertNoContent();
        // videos are soft deleted. So make sure that they exist in the database
        $this->assertDatabaseCount((new Video())->getTable(), $count);
    }
}
