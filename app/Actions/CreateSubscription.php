<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\EmailTemplate;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Exceptions\InvalidPaymentMethod as InvalidPaymentMethodAlias;

/**
 * Class CreateSubscription
 * @package App\Actions
 */
class CreateSubscription
{
    /**
     * @param User $user
     * @param string $paymentMethodId - payment method id stored in stripe. It's always a big string
     * @param Plan $plan
     * @return Subscription|\Laravel\Cashier\Subscription
     * @throws ActionException
     * @throws IncompletePayment
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public static function execute(User $user, string $paymentMethodId, Plan $plan): Subscription|\Laravel\Cashier\Subscription
    {
        try {
            /**@var Package $package */
            $package = $plan->package;

            $subscription = $user->subscription($package->name);

            // check if user already subscribed to the package
            if ($subscription) {
                // recurring means keep on getting deducted every month/monthly billing
                $recurring = $subscription->recurring();
                if ($recurring) throw new ActionException(__('Already subscribed to this package'));
            }

            // check if the payment method exist for the user or not
            // A payment method is basically card details saved of a card
            $methodExist = $user->findPaymentMethod($paymentMethodId);
            if (is_null($methodExist)) throw new ActionException(__('Invalid payment method'));

            /**
             * if the user is on grace period,
             * then resumes the subscription.
             * Grace period means -
             * Example - User subscribes on 1st jan for a monthly plan, the plan is getting expired on 30th Jan since
             * it's a monthly plan. Now on 10th Jan if the user cancels the subscription the plan is still active
             * because the user already paid for a month, it's just that the user is not automatically billed anymore
             * since the user cancelled subscription. This is what grace period means. It means the recurring plan is cancelled
             * but completely not expired yet.
             * If the user is in grace period, just resume subscription.
             * This will auto start the billing on 30th Jan and will continue.
             *
             * ---------------------------------------
             * Note - billing starts on billing cycle.
             * Not immediately
             * ----------------------------------------
             */
            if ($subscription && $subscription->onGracePeriod()) {
                $subscription->resume();
            } else {
                /**@var Subscription|\Laravel\Cashier\Subscription $subscription */
                $subscription = $user->newSubscription($package->name, $plan->stripe_plan_id)->create($paymentMethodId);
                SendSubscriptionMail::execute($user, $subscription, EmailTemplate::SUB_START);
            }

            return $subscription;
        } catch (InvalidPaymentMethodAlias) {
            throw new ActionException(__('The payment method does not belong to this user'));
        } catch (ActionException $e) {
            throw new ActionException($e->getMessage());
        }
    }
}
