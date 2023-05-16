<?php

namespace Database\Factories;

use App\Models\IncomeRange;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // don't change the password. The tests depend on this password
            'type' => Arr::random(['entrepreneur', 'founder']),
            'dob' => $this->faker->dateTimeBetween('-50 years', now()->subYears(30)->format('Y-m-d')),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'income_range_id' => $this->faker->randomElement(IncomeRange::all()->pluck('id')->toArray()),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function entrepreneur()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::ENTR,
            ];
        });
    }

    public function founder()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::FOUNDER,
            ];
        });
    }
}
