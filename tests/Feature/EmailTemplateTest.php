<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Tests\AppBaseTestCase;

/**
 * Tests related to email templates
 * Class EmailTemplateTest
 * @package Tests\Feature
 */
class EmailTemplateTest extends AppBaseTestCase
{
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

    private string $endpoint = 'api/v1/email-template';

    public function test_validation_error()
    {
        $this->authSuperAdmin();
        $req = $this->post($this->endpoint);
        $req->assertJsonValidationErrors(['subject', 'body']);
    }

    /**
     * @param TestResponse $res
     * @param $data
     */
    private function checkTemplateValues(TestResponse $res, $data)
    {
        $res->assertJson(fn(AssertableJson $json) => $json
            ->has('data.id')
            ->where('data.subject', Arr::get($data, 'subject'))
            ->where('data.comment', Arr::get($data, 'comment'))
            ->where('data.body', Arr::get($data, 'body'))
            ->etc());
    }

    /**
     * @return array
     */
    private function templateData(): array
    {
        return [
            'subject' => Str::random(),
            'body' => Str::random(),
            'comment' => Str::random(),
        ];
    }

    public function test_create_successful()
    {
        $data = $this->templateData();
        $this->authSuperAdmin();
        $req = $this->post($this->endpoint, $data);
        $req->assertCreated();
        $this->checkTemplateValues($req, $data);

        $this->assertDatabaseHas((new EmailTemplate())->getTable(), [
            'subject' => Arr::get($data, 'subject')
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        EmailTemplate::factory()->count(3)->create();
    }

    public function test_update()
    {
        $this->authSuperAdmin();
        $data = $this->templateData();
        $template = EmailTemplate::first();
        $req = $this->put($this->endpoint . '/' . $template->id, $data);
        $req->assertSuccessful();
        $this->checkTemplateValues($req, $data);
    }

    public function test_forbidden_by_anyone_else()
    {
        // it should be forbidden by anyone else apart from super admin
        $this->authStaff();
        $req = $this->post($this->endpoint);
        $req->assertForbidden();
    }

    public function test_delete()
    {
        $this->authSuperAdmin();
        $template = EmailTemplate::query()->first();
        $req = $this->delete($this->endpoint . '/' . $template->id);
        $req->assertNoContent();

        $deleted = EmailTemplate::query()->onlyTrashed()->where('id', $template->id)->first();
        $this->assertTrue((bool)$deleted); // check if it was actually soft deleted or not
    }

    public function test_get_templates()
    {
        EmailTemplate::factory()->count(10)->create();
        $this->authSuperAdmin();
        $query = [
            'id' => EmailTemplate::query()->first()->id
        ];
        $req = $this->get(sprintf('%s?%s', $this->endpoint, http_build_query($query)));
        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.0.id', Arr::get($query, 'id'))
                ->has('data', 1)
                ->etc());

        $this->testPagination($this->endpoint);
    }
}
