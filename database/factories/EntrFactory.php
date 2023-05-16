<?php

namespace Database\Factories;

use App\Models\EducationBackground;
use App\Models\EntrepreneurProfile;
use App\Models\LangLevel;
use App\Models\Occupation;
use App\Models\PresentPost;
use App\Models\User;
use App\Models\WorkingStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class EntrFactory
 * @package Database\Factories
 */
class EntrFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EntrepreneurProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'address' => $this->faker->streetAddress(),
            'education_background_id' => $this->faker->randomElement(EducationBackground::all()->pluck('id')->toArray()),
            'school_name' => $this->faker->company(),
            'working_status_id' => $this->faker->randomElement(WorkingStatus::all()->pluck('id')->toArray()),
            'present_company' => $this->faker->company(),
            'present_post_id' => $this->faker->randomElement(PresentPost::all()->pluck('id')->toArray()),
            'present_post_other' => null,
            'occupation_id' => $this->faker->randomElement(Occupation::all()->pluck('id')->toArray()),
            'lang_level_id' => $this->faker->randomElement(LangLevel::all()->pluck('id')->toArray()),
            'en_lang_level_id' => $this->faker->randomElement(LangLevel::all()->pluck('id')->toArray()),
            'transfer' => $this->faker->randomElement(['yes', 'no', 'only domestic', 'only overseas']),
        ];
    }

    public function addUser()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => User::entrepreneurs()
                    ->whereNotIn('id', EntrepreneurProfile::select('user_id')->get()->pluck('user_id')->toArray())
                    ->first()
                    ->id,
            ];
        });
    }
}
