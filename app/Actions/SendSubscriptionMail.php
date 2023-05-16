<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\EmailTemplate;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\NotificationMail;

/**
 *
 * Class SendSubscriptionMail
 * @package App\Actions
 */
class SendSubscriptionMail
{
    /**
     * @param User $user
     * @param Subscription|\Laravel\Cashier\Subscription $subscription
     * @param string $name
     * @throws ActionException
     */
    public static function execute(User $user, Subscription|\Laravel\Cashier\Subscription $subscription, string $name)
    {
        $template = EmailTemplate::name($name)->first();
        if ($template) {
            $extract = (new ExtractTxt4mMailTemplate($template))
                ->setSubscription($subscription);
            $user->notify(new NotificationMail($template, $extract->getSubject(), $extract->getBody()));
        }
    }
}
