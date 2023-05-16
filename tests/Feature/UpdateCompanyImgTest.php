<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

/**
 * Test update of company logo and banner
 */
class UpdateCompanyImgTest extends AppBaseTestCase
{
    private string $endpoint = '/api/v1/update-company-imgs';

    public function test_validation_error()
    {
        $this->authFounder();
        $req = $this->post($this->endpoint, [
            'logo' => 'text', // should be image
            'banner' => 'text', // should be image
            'remove_logo' => 'text', // should be bool
            'remove_banner' => 'text' // should be bool
        ]);
        $req->assertJsonValidationErrors([
            'logo',
            'banner',
            'remove_logo',
            'remove_banner'
        ]);
    }

    public function test_only_founder_user()
    {
        // entr doesn't have access to this
        $this->authEntr();
        $req = $this->post($this->endpoint);
        $req->assertStatus(400)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('message', 'You can only update company images for a founder user')
                ->etc());
    }

    public function test_only_user_auth()
    {
        // no staff has access to this
        $this->authStaff();
        $req = $this->post($this->endpoint);
        $req->assertUnauthorized();
    }

    public function test_upload_logo()
    {
        Storage::fake('company');
        $user = $this->authFounder();
        $req = $this->post($this->endpoint, [
            'logo' => UploadedFile::fake()->image('logo.png')->size(1000)
        ]);
        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data')
                ->etc());

        $path = $user->fdrProfile->company_logo_path;
        Storage::disk('company')
            ->exists($path);

        // remove the logo
        $req2 = $this->post($this->endpoint, ['remove_logo' => true]);
        $req2->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.company_logo', null)
                ->etc());

        Storage::disk('company')
            ->missing($path);
    }

    public function test_upload_banner()
    {
        Storage::fake('company');
        $user = $this->authFounder();
        $req = $this->post($this->endpoint, [
            'banner' => UploadedFile::fake()->image('banner.png')->size(1000)
        ]);

        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data')
                ->etc());

        $path = $user->fdrProfile->company_banner_img_path;
        Storage::disk('company')
            ->exists($path);

        $req2 = $this->post($this->endpoint, ['remove_banner' => true]);
        $req2->assertSuccessful();
        Storage::disk('company')
            ->missing($path);
    }
}
