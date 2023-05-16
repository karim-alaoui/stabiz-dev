<?php


namespace App\Actions;


use App\Models\EmailTemplate;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

/**
 * Delete all active subscriptions
 * Class DelAllSub
 * @package App\Actions
 */
class DelAllSub
{
    /**
     * @param User|Authenticatable $user
     */
    public static function execute(User|Authenticatable $user)
    {
        /**@var Collection $subscriptions */
        $subscriptions = $user->subscriptions()->active()->get();
        $subscriptions->map(function (Subscription $subscription) use ($user) {
            $subscription->cancel();
            SendSubscriptionMail::execute($user, $subscription, EmailTemplate::SUB_CANCEL);
        });
    }
}
