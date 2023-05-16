<?php


namespace App\Actions;


use App\Models\User;
use Stripe\Customer;

/**
 * Class CreateBillingUser
 * @package App\Actions
 */
class CreateStripeCustIfNotExist
{
    /**
     * @param User $user
     * @return Customer
     */
    public static function execute(User $user): Customer
    {
        $user->createOrGetStripeCustomer();
        UpdateStripeCust::execute($user);
        return $user->asStripeCustomer();
    }
}
