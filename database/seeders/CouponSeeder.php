<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Stripe\StripeClient;

/**
 * Class CouponSeeder
 * @package Database\Seeders
 */
class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function run()
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        $stripe->coupons->create([
            'percent_off' => 25,
            'duration' => 'repeating',
            'duration_in_months' => 3,
        ]);
    }
}
