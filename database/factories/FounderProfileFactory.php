<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\FounderProfile;
use App\Models\Prefecture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class FounderProfileFactory
 * @package Database\Factories
 */
class FounderProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FounderProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_name' => $this->faker->company(),
            'is_listed_company' => db_bool_val($this->faker->randomElement([true, false])),
            'area_id' => $this->faker->randomElement(Area::all()->pluck('id')->toArray()),
            'prefecture_id' => $this->faker->randomElement(Prefecture::all()->pluck('id')->toArray()),
            'no_of_employees' => 200,
            'capital' => 324927,
            'last_year_sales' => 198738,
            'established_on' => '1995-10-01',
            'business_partner_company' => $this->faker->company(),
            'major_bank' => $this->faker->company(),
            'company_features' => implode(' <br/>', $this->faker->paragraphs()),
            'job_description' => implode(' <br/>', $this->faker->paragraphs()),
            'application_conditions' => implode(' <br/>', $this->faker->paragraphs()),
            'employee_benefits' => implode(' <br/>', $this->faker->paragraphs())
        ];
    }
}
