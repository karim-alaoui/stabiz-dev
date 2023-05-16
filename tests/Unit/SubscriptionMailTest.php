<?php

namespace Tests\Unit;

use App\Actions\SendSubscriptionMail;
use App\Exceptions\ActionException;
use App\Models\EmailTemplate;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\NotificationMail;
use Illuminate\Support\Facades\Notification;
use Tests\AppBaseTestCase;

/**
 * Class SubscriptionMailTest
 * @package Tests\Unit
 */
class SubscriptionMailTest extends AppBaseTestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    /**
     * @throws ActionException
     */
    public function test_sub_mail_sent()
    {
        Notification::fake();

        User::factory()
            ->has(Subscription::factory()->count(1), 'subscriptions')
            ->create();

        $user = User::first();
        SendSubscriptionMail::execute($user, Subscription::first(), EmailTemplate::SUB_START);
        Notification::assertSentTo([$user], NotificationMail::class);
    }
}
