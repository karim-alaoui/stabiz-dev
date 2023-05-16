<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\EmailTemplate;
use App\Models\Package;
use App\Models\Recommendation;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\NotificationMail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

/**
 * Apply to entrepreneur/founders
 * Class ApplyTesting
 * @package Tests\Feature
 */
class ApplicationApplyTest extends AppBaseTestCase
{
    protected ?User $entrUser = null;
    protected ?User $fdrUser = null;
    protected ?Staff $staff = null;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->entrepreneur()->count(3)->create();
        User::factory()->founder()->count(3)->create();

        $this->entrUser = User::factory()->entrepreneur()->create();
        $this->fdrUser = User::factory()->founder()->create();

        /**
         * An user would apply to someone when it comes into their recommendation list.
         * Recommendation list would come after a staff has recommended an user to another user.
         * So, create this recommendation list. We would need to check if the staff is notified
         * when this recommended_to_user applies to recommended_user
         */
        $recommendation = Recommendation::factory()->state([
            'recommended_to_user_id' => $this->entrUser->id,
            'recommended_user_id' => $this->fdrUser->id
        ])->create();

        $this->entrUser->newSubscription(Package::PREMIUM);
        $this->staff = $recommendation->staff;
    }

    private string $endpoint = '/api/v1/applications';

    public function test_apply_success_with_notification()
    {
        Notification::fake();
        // test that user is able to apply successfully and the staff is notified of that

        /**@var Recommendation $recommendation */
        $recommendation = Recommendation::first();
        $user = $recommendation->recommendedToUser;
        Passport::actingAs($user);

        Subscription::factory()->state([
            'user_id' => $user->id
        ])->create(); // create the subscription since the user will have to be a premium user to apply

        // let the user to whom the other user was recommended, if the recommended to user
        // applied to the recommended user, then notify the staff who made that recommendation
        $applyToUserId = $recommendation->recommendedUser->id;
        $req = $this->post($this->endpoint, ['apply_to_user_id' => $applyToUserId]);
        $req->assertSuccessful();

        Notification::assertSentTo($recommendation->staff, function (NotificationMail $notification) {
            // make sure that this notification is sent
            $template = EmailTemplate::name(EmailTemplate::USER_APPLIED_NOTIFY_STAFF)->first();
            return $notification->template->id == $template->id;
        });

        /**
         * Check if when checking if the user applied to this user
         * if it returns true or not for applied key
         */
        $checkEndpoint = fn($userId = null) => '/api/v1/applications/check/' . ($userId ?: $applyToUserId);
        $req2 = $this->get($checkEndpoint());
        $req2->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('applied', true)
                ->etc());

        /**
         * Check with a random user id and check if it returns that it didn't apply to the user
         */
        $req3 = $this->get($checkEndpoint(22222222222)); // just provide a random id here
        $req3->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('applied', false)
                ->etc());
    }

    public function test_validation_error()
    {
        $this->authEntr();
        $req = $this->post($this->endpoint);
        $req->assertJsonValidationErrors(['apply_to_user_id']);
    }

    public function test_fdr_apply_fdr()
    {
        // user can only apply to opposite user type
        // should return error if founder apply to founder
        $this->authFounder();
        $founder = User::query()->founders()->first();
        $req = $this->post($this->endpoint, ['apply_to_user_id' => $founder->id]);
        $req->assertStatus(400)
            ->assertJson(fn(AssertableJson $json) => $json->has('message')->etc());
    }

    public function test_entr_to_entr()
    {
        // entr can't apply to entr
        $this->authEntr();
        $entr = User::query()
            ->entrepreneurs()
            ->first();
        $req = $this->post($this->endpoint, ['apply_to_user_id' => $entr->id]);
        $req->assertStatus(400)
            ->assertJson(fn(AssertableJson $json) => $json->has('message')->etc());
    }

    /**
     * @param $applyToType
     */
    private function apply($applyToType)
    {
        $user = User::query()
            ->where('type', $applyToType)
            ->first();

        $req = $this->post($this->endpoint, ['apply_to_user_id' => $user->id]);
        $req->assertCreated();

        // should return 400 since already applied
        $req = $this->post($this->endpoint, ['apply_to_user_id' => $user->id]);
        $req->assertStatus(400)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('message', 'Already applied to this user')
                ->etc());

        $this->assertDatabaseHas((new Application())->getTable(), [
            'applied_to_user_id' => $user->id
        ]);
    }

    // TODO: write tests for stripe api

    /*public function test_assert_successful_by_entr()
    {
        Passport::actingAs($this->entrUser);
        $user = $this->entrUser;
        Log::info(json_encode($user->subscriptions));
//        $this->apply(User::FOUNDER);
    }

    public function test_assert_successful_by_fdr()
    {
        $this->authFounder();
        $this->apply(User::ENTR);
    }*/

    public function test_accept_success()
    {
        Notification::fake();
        /**@var Application $application */
        $application = Application::factory()->create();
        /**@var User $appliedTo */
        $appliedTo = $application->appliedTo;
        Passport::actingAs($appliedTo);

        $response = $this->post($this->endpoint . '/accept/' . $application->id);
        $response->assertSuccessful(); // user can only accept if the application to applied to him/her

        // check if the RIGHT notification was sent
        Notification::assertSentTo([$application->appliedBy], function (NotificationMail $notification) {
            return $notification->template->id == EmailTemplate::query()->where('name', 'ilike', EmailTemplate::APPL_ACCEPTED)->first()->id;
        });

        $updateApl = Application::find($application->id);
        // check if the application was actually updated on the database level
        // if accepted, then it would have accepted_at with some value and rejected_at would be null
        $this->assertTrue((bool)($updateApl->accepted_at && $updateApl->rejected_at === null));
    }

    public function test_accept_forbidden()
    {
        Queue::fake();
        $application = Application::factory()->create();
        /**@var User $appliedBy */
        $appliedBy = $application->appliedBy;
        Passport::actingAs($appliedBy);
        $response2 = $this->post($this->endpoint . '/accept/' . $application->id);
        $response2->assertForbidden(); // should be forbidden since this was not applied to the user

        Queue::assertNothingPushed(); // no notification should be pushed. Usually, if accepted then it should be sent.
    }
}
