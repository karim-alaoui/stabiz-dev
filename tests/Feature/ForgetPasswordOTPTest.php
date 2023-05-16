<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\OTPCodeNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\AppBaseTestCase;

class ForgetPasswordOTPTest extends AppBaseTestCase
{
    /**
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    private function makeReq(array $data = [], array $headers = []): TestResponse
    {
        return $this->post('api/v1/forget-password', $data, $headers);
    }

    public function test_validation_error()
    {
        $res = $this->makeReq();
        $res->assertJsonValidationErrors(['email']);

        $res = $this->makeReq(['email' => 'notanemail']);
        $res->assertJsonValidationErrors(['email']);
    }

    // if the user email doesn't exist in the system, then it should not send any notification
    public function test_otp_not_sent()
    {
        Notification::fake();
        $email = 'random1@mail.com';
        $res = $this->makeReq(['email' => $email]);
        $res->assertSuccessful(); // should return success but should not send the email
        Notification::assertNothingSent();
    }

    public function test_otp_actually_sent()
    {
        Notification::fake();
        $user = User::factory()->create();

        // send the lang as ja (japanese)
        // we will check if the proper subject is sent or not
        $res = $this->makeReq(['email' => $user->email], ['lang' => 'ja']);
        $res->assertSuccessful();

        /*Notification::assertSentTo(
            new AnonymousNotifiable(), OTPCodeNotification::class
        );*/
        $subject = null;
        Notification::assertSentTo(new AnonymousNotifiable(), function (OTPCodeNotification $notification) use (&$subject) {
            $subject = $notification->subject;
            return true; // we do it like this just to get the otp which was sent in the mail
        });

        // make sure that this subject is used
        $this->assertTrue($subject === __('phrase.otp_subject'));
    }
}
