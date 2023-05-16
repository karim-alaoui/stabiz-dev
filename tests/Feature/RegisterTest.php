<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\OTPCodeNotification;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

class RegisterTest extends AppBaseTestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/register/entrepreneur';

    private array $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'email' => 'random1@mail.com',
            'password' => 'password123',
        ];
    }

    public function test_method_post()
    {
        $res = $this->get($this->url);
        $res->assertStatus(405);
    }

    public function test_validation_error()
    {
        $res = $this->post($this->url);
        $res->assertStatus(422);
        $res->assertJson(fn(AssertableJson $json) => $json->has('errors.email')
//            ->has('errors.income_range_id')
            ->has('errors.password')
            ->etc());
    }

    /*public function test_password_validation()
    {
        $data = $this->data;
        $data['password'] = 'basicpassword';
        $res = $this->post($this->url, $data);
        $res->assertStatus(422);
        $res->assertJson(fn(AssertableJson $json) => $json->has('errors.password')
            ->missing('errors.email')
            ->missing('errors.income_range_id')
            ->etc());
    }*/

    /**
     * @throws Exception
     */
    private function createUser($type)
    {
        App::setLocale('ja'); // set the locale to english because we need to test the subject of the notification
        Notification::fake();
        $data = $this->data;

        if ($type == 'entrepreneur') {
            $url = $this->url;
        } elseif ($type == 'founder') {
            $url = 'api/v1/register/founder';
        } else {
            throw new Exception('Invalid type');
        }

        $res = $this->post($url, $data);
        Notification::assertSentTo(
            new AnonymousNotifiable(),
            OTPCodeNotification::class,
            function ($notification) {
                // verify that the correct email subject was sent
                return $notification->subject == '【STABIZ】会員登録用ワンタイムパスワード';
            }
        );
        $res->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.id')
                ->where('data.email', $this->data['email'])
                ->where('data.type', $type)
                ->etc()
            );
    }

    /**
     * @throws Exception
     */
    public function test_entrepreneur_account_created()
    {
        $this->createUser(User::ENTR);
    }

    /**
     * @throws Exception
     */
    public function test_founder_profile_created()
    {
        $this->createUser(User::FOUNDER);
    }

    public function test_user_creation_using_same_email()
    {
        // same email should be allowed for different user types
        // however, not for same user type
        $endpointEntr = 'api/v1/register/entrepreneur';
        $endpointFdr = 'api/v1/register/founder';

        $reqBody = [
            'email' => 'user@example.com',
            'password' => 'password123*'
        ];
        $reqEnt = fn() => $this->post($endpointEntr, $reqBody);
        $req1 = $reqEnt();
        $req1->assertSuccessful();

        // already registered an entr using this email
        // this should not be allowed
        $req2 = $reqEnt();
        $req2->assertJsonValidationErrors(['email'])
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('errors.email.0', 'The email has already been taken.')
                ->etc());

        $reqFdr = fn() => $this->post($endpointFdr, $reqBody);
        $req3 = $reqFdr();
        $req3->assertSuccessful();

        // already registered a founder using this email
        // should not be allowed to register again
        $req4 = $reqFdr();
        $req4->assertJsonValidationErrors(['email'])
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('errors.email.0', 'The email has already been taken.')
                ->etc());
    }
}
