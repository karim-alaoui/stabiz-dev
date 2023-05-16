<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

/**
 * Test related to user's display picture
 * Class UserProfilePicTest
 * @package Tests\Feature
 */
class UserProfilePicTest extends AppBaseTestCase
{
    private string $endpoint = '/api/v1/update-dp';

    public function test_validation_error()
    {
        $this->authUser();
        $res = $this->post($this->endpoint, ['photo' => 'string']);
        $res->assertJsonValidationErrors('photo');
    }

    public function test_validation_error_size()
    {
        Storage::fake('avatars');
        $this->authUser();
        $file = UploadedFile::fake()->image('img.jpg')->size(9000);
        $res = $this->post($this->endpoint, ['photo' => $file]);
        $res->assertJsonValidationErrors(['photo'])
            ->assertJsonPath('errors.photo.0', 'Max file size is 4mb');
    }

    public function test_validation_file_type()
    {
        Storage::fake('avatars');
        $this->authUser();

        $file = UploadedFile::fake()->create(
            'doc.pdf',
            9000,
            'application/pdf'
        );

        $res = $this->post($this->endpoint, ['photo' => $file]);
        $res->assertJsonValidationErrors(['photo']);
    }

    private function storage()
    {
        Storage::fake('avatars');
    }

    public function test_successful_update()
    {
        Storage::fake('avatars');
        $user = $this->authUser();

        $file = UploadedFile::fake()->image('img.jpg')->size(1000);
        $res = $this->post($this->endpoint, [
            'photo' => $file
        ]);

        $res->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->whereType('data.avatar', 'string')
                ->etc());

        $found = User::query()
            ->whereNotNull('dp_disk') // this two columns will be filled if the dp is updated
            ->whereNotNull('dp_full_path')
            ->where('id', $user->id)
            ->first();
        $this->assertTrue((bool)$found);

        Storage::disk('avatars')->assertMissing($found->dp_full_path); // check if the file uploaded on the storage

        // remove the dp by sending photo = null
        // check if the user fields are updated to null and file is deleted from storage

        $res2 = $this->post($this->endpoint, ['photo' => null]);
        $res2->assertSuccessful();
        Storage::disk('avatars')->assertMissing($found->dp_full_path); // check that file is deleted from storage

        $deleted = User::query()->find($user->id);
        $this->assertTrue(is_null($deleted->dp_full_path)); // this field should be updated to null
        $this->assertTrue(is_null($deleted->dp_disk));
    }
}
