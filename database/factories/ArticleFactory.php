<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * Class ArticleFactory
 * @package Database\Factories
 */
class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('ja_JP');
        return [
            'title' => substr((string)$faker->paragraphs(1, true), 0, random_int(20, 50)),
            'content' => $faker->paragraphs(10, true),
            'description' => $faker->paragraphs(2, true),
            'publish_after' => now()->addDays(random_int(0, 10)),
            'hide_after' => now()->addDays(random_int(11, 20)),
            'is_draft' => $faker->randomElement([DB::raw('true'), DB::raw('false')])
        ];
    }
}
