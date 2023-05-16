<?php

namespace Database\Factories;

use App\Models\Recommendation;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecommendationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Recommendation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'by_staff_id' => Staff::factory()->create()->id,
            'recommended_to_user_id' => User::factory()->state(['type' => User::ENTR])->create()->id,
            'recommended_user_id' => User::factory()->state(['type' => User::FOUNDER])->create()->id,
        ];
    }

    /**
     * Only entrepreneurs are recommended
     * @return RecommendationFactory
     */
    public function onlyEntrRecommended(): RecommendationFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'recommended_user_id' => User::factory()->state(['type' => User::ENTR])->create()->id
            ];
        });
    }

    /**
     * Only founders are recommended
     * @return RecommendationFactory
     */
    public function onlyFdrRecommended(): RecommendationFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'recommended_user_id' => User::factory()->state(['type' => User::FOUNDER])->create()->id
            ];
        });
    }
}
