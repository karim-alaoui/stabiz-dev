<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Subscription as CashierSubscription;

/**
 * We use this model as the default subscription model for cashier (stripe)
 * Class Subscription
 * @package App\Models
 */
class Subscription extends CashierSubscription
{
    use HasFactory;

    // use these two constants while testing the application
    /**
     * Testing method id used in running tests in the application
     * If you remove the payment method from stripe, replace this with the new payment method id generated
     * @var string
     */
    public const TESTING_PAYMENT_METHOD = 'card_1JHMyoEq66SW1hg3KsDtvdKa';

    /**
     * Testing email of the customer which is used in the testing of the application
     * The payment method is linked to this email customer in stripe
     * @var string
     */
    public const TESTING_CUST_EMAIL = 'lonie83@example.net';

    /**
     * By default, it was loading some relations which
     * were increasing the load. Thus getting rid of them here
     */
    protected $with = [];

    /**
     * Basically, the price plan
     * @return HasOne
     */
    public function subscriptionItem(): HasOne
    {
        return $this->hasOne(SubscriptionItem::class);
    }
}
