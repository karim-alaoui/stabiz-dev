<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 *
 */
class ApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Application::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $roles = [User::ENTR, User::FOUNDER];
        $appliedToRole = Arr::random($roles);
        $appliedByRole = $appliedToRole === User::ENTR ? User::FOUNDER : User::ENTR;
        return [
            'applied_to_user_id' => User::factory()->create(['type' => $appliedToRole])->id,
            'applied_by_user_id' => User::factory()->create(['type' => $appliedByRole])->id,
        ];
    }
}
