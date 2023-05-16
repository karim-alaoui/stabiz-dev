<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class SubscriptionFactory
 * @package Database\Factories
 */
class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $package = Package::query()->inRandomOrder()->first();
        $plan = Plan::query()->inRandomOrder()->first();
        return [
            'name' => $package->name,
            'stripe_id' => Str::random(),
            'stripe_status' => 'active',
            'stripe_price' => $plan->stripe_plan_id,
            'user_id' => User::factory()->create()->id,
            'quantity' => 1
        ];
    }
}
