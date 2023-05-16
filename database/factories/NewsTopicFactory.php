<?php

namespace Database\Factories;

use App\Models\NewsTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsTopicFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NewsTopic::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(30),
            'body' => $this->faker->realText(),
            'show_after' => now(),
            'hide_after' => now()->addDays(150)->format('Y-m-d')
        ];
    }

    public function past()
    {
        return $this->state(function (array $attributes) {
            return [
                'show_after' => now()->subDays(20)->format('Y-m-d'),
                'hide_after' => now()->subDays(10)->format('Y-m-d')
            ];
        });
    }

    public function future()
    {
        return $this->state(function (array $attributes) {
            return [
                'show_after' => now()->addDays(1)->format('Y-m-d'),
                'hide_after' => now()->addDays(10)->format('Y-m-d')
            ];
        });
    }
}
