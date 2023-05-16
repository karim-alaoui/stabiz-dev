<?php

namespace Tests\Feature;

use App\Models\OTP;
use App\Models\User;
use App\Notifications\OTPCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;


/**
 * Class OTPTest
 * @package Tests\Feature
 */
class OTPTest extends AppBaseTestCase
{
    use RefreshDatabase;

    private string $send_url = '/api/v1/otp-send';
    private string $email = 'random1@mail.com';

    /**
     * The HTTP verb should be post
     */
    public function test_if_method_not_allowed()
    {
        $response = $this->get($this->send_url);
        $response->assertStatus(405);
    }

    public function test_if_validation_error()
    {
        $response = $this->post('/api/v1/otp-send');
        $response->assertStatus(422);
        $response->assertJson(fn(AssertableJson $json) => $json->has('errors.email')->etc());
    }

    public function test_if_otp_sent()
    {
        App::setLocale('ja'); // set the locale to Japanese because we need to test the subject of the notification
        $email = $this->email;
        Notification::fake();
        $response = $this->post('/api/v1/otp-send', [
            'email' => $email,
            'registration' => true // send it as true to check if during registration the correct subject is sent or not
        ]);
        $response->assertStatus(200);
        $otp = OTP::where('email', $email)->latest()->first();
        self::assertTrue((bool)$otp);
        Notification::assertSentTo(
            new AnonymousNotifiable(),
            OTPCodeNotification::class,
            function ($notification) {
                // verify that the correct email subject was sent
                return $notification->subject == '【STABIZ】会員登録用ワンタイムパスワード';
            }
        );
    }

    public function test_if_otp_fail()
    {
        Notification::fake();
        $email = 'eamil@mail.com';
        $this->post($this->send_url, ['email' => $email]);
        $response = $this->post('/api/v1/otp-verify');
        $response->assertJsonValidationErrors(['email', 'otp', 'user_type']);

        // since this is some random OTP and not the actual one, it should fail with 400 code
        $response2 = $this->post('/api/v1/otp-verify', ['email' => $email, 'otp' => '000000', 'user_type' => User::FOUNDER]);
        $response2->assertStatus(400);
    }

    /**
     * @param $otp
     * @param string $email
     * @param string $userType
     */
    private function createOTP($otp, string $email = 'random1@mail.com', string $userType = 'founder')
    {
        OTP::create([
            'email' => $email,
            'otp' => Hash::make($otp),
        ]);

        User::create([
            'email' => $email,
            'password' => Hash::make('password'),
            'type' => $userType
        ]);
    }

    public function test_otp_verify()
    {
        $url = '/api/v1/otp-verify';
        $email = 'random1@mail.com';
        $otp = '123456';
        $userType = User::FOUNDER;
        $this->createOTP($otp, $email);
        $user = User::emailAndType($email, $userType)->first();
        self::assertEmpty($user->email_verified_at);

        $response1 = $this->post($url, ['email' => $email, 'otp' => '234567', 'user_type' => $userType]);
        $response1->assertStatus(400); //should fail

        $response2 = $this->post($url, ['email' => $email, 'otp' => $otp, 'user_type' => $userType]);
        $response2->assertStatus(200);

        // after otp is verified, mail should be marked as verified
        $user->refresh();
        self::assertTrue((bool)$user->email_verified_at);
    }

    public function test_if_fail_if_requested_more_than_limit()
    {
        Notification::fake();
        // you can get only 2 otps in 1 minute
        $this->test_if_otp_sent();
        $this->test_if_otp_sent();

        // before each request was counted and you could not request more than 2
        // even if you could not pass some validation test, still it was limited 2
        // which is not fair. It should return validation error and not 429
        // make sure that it's returning validation error now even after requesting 2 successful OTPs
        $endpoint = '/api/v1/otp-send';
        $req1 = $this->post($endpoint);
        $req1->assertJsonValidationErrors(['email']);

        // if you provide everything, make sure that it returns that it can't send any more OTPs for a min now
        $req2 = $this->withHeaders(['lang' => 'en'])->post($endpoint, [
            'email' => $this->email,
            'registration' => true // send it as true to check if during registration the correct subject is sent or not
        ]);
        $req2->assertStatus(400)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('message', 'Please wait a minute before requesting again')
                ->etc());

        // make sure to test again by travelling 1 minute in the future
        // to see if you're able to receive OTP
//        $this->travel(61)->seconds(); // keep it 61 seconds and not 60. It would fail at 60
//        $this->test_if_otp_sent();
    }
}
