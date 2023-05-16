<?php

namespace Database\Factories;

use App\Models\ArticleAudience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ArticleAudienceFactory
 * @package Database\Factories
 */
class ArticleAudienceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArticleAudience::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'audience' => $this->faker->randomElement(['entrepreneur', 'founder'])
        ];
    }
}
