<?php

namespace Database\Factories;

use App\Models\FdrPfdIndustry;
use App\Models\Industry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class FdrPfdIndustryFactory
 * @package Database\Factories
 */
class FdrPfdIndustryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FdrPfdIndustry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'industry_id' => Industry::query()->inRandomOrder()->first()->id
        ];
    }
}
